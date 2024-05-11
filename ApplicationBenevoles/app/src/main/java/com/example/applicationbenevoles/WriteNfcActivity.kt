package com.example.applicationbenevoles

import android.annotation.SuppressLint
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.nfc.NdefMessage
import android.nfc.NdefRecord
import android.nfc.NfcAdapter
import android.nfc.NfcManager
import android.nfc.Tag
import android.nfc.tech.Ndef
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.toolbox.StringRequest
import com.android.volley.toolbox.Volley
import org.json.JSONArray
import org.json.JSONException

class WriteNfcActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE "
    private lateinit var adapter: NfcAdapter
    private lateinit var editText: EditText
    private lateinit var writeNfcButton: Button
    private var tagFromIntent: Tag? = null

    private lateinit var accessToken: String


    @SuppressLint("SetTextI18n")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        Log.w(logTag, "WriteNfc Created")
        setContentView(R.layout.activity_write_nfc)
        editText = findViewById(R.id.editText)
        writeNfcButton = findViewById(R.id.writeNfcButton)


        accessToken = intent.getStringExtra("access_token") ?: ""
        Log.d(logTag, accessToken)


        writeNfcButton.setOnClickListener {
            val nfcText = editText.text.toString()
            Log.d(logTag, "Write NFC button clicked with text: $nfcText")
            checkIdExistence(nfcText, accessToken)
        }

        initNfcAdapter()
    }

    private fun initNfcAdapter() {
        val nfcManager = getSystemService(Context.NFC_SERVICE) as NfcManager
        adapter = nfcManager.defaultAdapter
        if (adapter == null) {
            Log.e(logTag, "NFC Adapter is null. This device may not support NFC.")
        }
    }

    private fun enableNfcForegroundDispatch() {
        try {
            val intent = Intent(this, javaClass).addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP)
            val nfcPendingIntent = PendingIntent.getActivity(
                this, 0, intent, PendingIntent.FLAG_MUTABLE
            )
            adapter.enableForegroundDispatch(this, nfcPendingIntent, null, null)
            Log.d(logTag, "NFC foreground dispatch enabled")
        } catch (ex: IllegalStateException) {
            Log.e(logTag, "Error enabling NFC foreground dispatch", ex)
        }
    }

    private fun disableNfcForegroundDispatch() {
        try {
            adapter.disableForegroundDispatch(this)
            Log.d(logTag, "NFC foreground dispatch disabled")
        } catch (ex: IllegalStateException) {
            Log.e(logTag, "Error disabling NFC foreground dispatch", ex)
        }
    }

    override fun onResume() {
        super.onResume()
        if (checkNFCDeviceSupport()) {
            enableNfcForegroundDispatch()
        }
    }

    override fun onPause() {
        super.onPause()
        if (checkNFCDeviceSupport()) {
            disableNfcForegroundDispatch()
        }
    }

    private fun checkNFCDeviceSupport(): Boolean {
        val isSupported = true
        Log.d(logTag, "NFC Supported: $isSupported")
        if (!isSupported) {
            Log.e(logTag, "NFC NOT supported on this device!")
            return false
        } else if (!adapter.isEnabled) {
            Log.e(logTag, "NFC NOT Enabled!")
            return false
        }
        return true
    }

    override fun onNewIntent(intent: Intent) {
        super.onNewIntent(intent)
        tagFromIntent = intent.getParcelableExtra(NfcAdapter.EXTRA_TAG)
        if (tagFromIntent != null) {
            Log.d(logTag, "NFC_TAG received")
            val nfcText = editText.text.toString()
            checkIdExistence(nfcText, accessToken)
        } else {
            Log.d(logTag, "No NFC_TAG received")
            Toast.makeText(this, "Please tap an NFC tag", Toast.LENGTH_SHORT).show()
        }
    }

    private fun checkIdExistence(nfcText: String, accessToken: String) {
        val url = getString(R.string.server_url_info) + "/all"
        Log.d(logTag, "Checking ID existence for text: $nfcText")
        val stringRequest = object : StringRequest(Method.GET, url,
            { response ->
                try {
                    Log.d(logTag, "Response received: $response")
                    val jsonArray = JSONArray(response)

                    var isValid = false
                    for (i in 0 until jsonArray.length()) {
                        val user = jsonArray.getJSONObject(i)
                        val userId = user.getInt("id").toString()
                        Log.w(logTag, userId)
                        if (userId == nfcText) {
                            isValid = true
                            break
                        }
                    }

                    if (isValid) {
                        writeNfcTag(nfcText)
                    } else {
                        Toast.makeText(this, "ID is not valid", Toast.LENGTH_SHORT).show()
                    }
                } catch (e: JSONException) {
                    Log.e(logTag, "Error parsing validation JSON: ${e.message}")
                }
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "Error validating ID: $errorMessage")
                error.printStackTrace()
                Toast.makeText(this, errorMessage, Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers: MutableMap<String, String> = HashMap()
                headers["Authorization"] = "Bearer $accessToken"
                Log.d(logTag, headers.toString())
                return headers
            }
        }

        Volley.newRequestQueue(this).add(stringRequest)
    }



    private fun writeNfcTag(nfcText: String) {
        val ndef = Ndef.get(tagFromIntent)
        if (ndef != null) {
            try {
                ndef.connect()
                ndef.writeNdefMessage(createNdefMessage(nfcText))
                Toast.makeText(this, "NFC Tag written successfully", Toast.LENGTH_SHORT).show()

                Log.i(logTag, "Starting HomeActivity...")
                val newIntent = Intent(this, HomeActivity::class.java)
                newIntent.putExtra("NFC_TEXT", nfcText)
                startActivity(newIntent)
            } catch (e: Exception) {
                Log.e(logTag, "Error writing to NFC Tag", e)
                Toast.makeText(this, "Error writing to NFC Tag", Toast.LENGTH_SHORT).show()
            } finally {
                ndef.close()
            }
        } else {
            Log.d(logTag, "NFC Tag does not support NDEF")
            Toast.makeText(this, "NFC Tag does not support NDEF", Toast.LENGTH_SHORT).show()
        }
    }

    private fun createNdefMessage(nfcText: String): NdefMessage {
        val ndefRecord = NdefRecord.createTextRecord(null, nfcText)
        return NdefMessage(arrayOf(ndefRecord))
    }
}
