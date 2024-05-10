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

class WriteNfcActivity : AppCompatActivity() {

    private val logTag = "NFC_APP"
    private lateinit var adapter: NfcAdapter
    private lateinit var editText: EditText
    private lateinit var writeNfcButton: Button
    private var tagFromIntent: Tag? = null

    @SuppressLint("SetTextI18n")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        Log.w(logTag, "WriteNfc Created")
        setContentView(R.layout.activity_write_nfc)
        editText = findViewById(R.id.editText)
        writeNfcButton = findViewById(R.id.writeNfcButton)

        writeNfcButton.setOnClickListener {
            tagFromIntent?.let { tag ->
                val nfcText = editText.text.toString()
                writeNfc(tag, nfcText)
            } ?: run {
                Toast.makeText(this, "Please tap an NFC tag", Toast.LENGTH_SHORT).show()
            }
        }

        initNfcAdapter()
    }

    private fun initNfcAdapter() {
        val nfcManager = getSystemService(Context.NFC_SERVICE) as NfcManager
        adapter = nfcManager.defaultAdapter
    }

    private fun enableNfcForegroundDispatch() {
        try {
            val intent = Intent(this, javaClass).addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP)
            val nfcPendingIntent = PendingIntent.getActivity(
                this, 0, intent, PendingIntent.FLAG_MUTABLE
            )
            adapter.enableForegroundDispatch(this, nfcPendingIntent, null, null)
        } catch (ex: IllegalStateException) {
            Log.e(logTag, "Error enabling NFC foreground dispatch", ex)
        }
    }

    private fun disableNfcForegroundDispatch() {
        try {
            adapter.disableForegroundDispatch(this)
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
            writeNfc(tagFromIntent!!, nfcText)
        } else {
            Log.d(logTag, "No NFC_TAG received")
            Toast.makeText(this, "Please tap an NFC tag", Toast.LENGTH_SHORT).show()
        }
    }

    private fun writeNfc(tag: Tag, nfcText: String) {
        Log.d(logTag, "Writing NFC tag...")
        val ndef = Ndef.get(tag)
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

