package com.example.applicationbenevoles

import android.annotation.SuppressLint
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.nfc.NfcAdapter
import android.nfc.NfcManager
import android.nfc.Tag
import android.nfc.tech.Ndef
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

class ReadNfcActivity : AppCompatActivity() {

    private val logTag = "NFC_APP"
    private lateinit var adapter: NfcAdapter
    private var tagFromIntent: Tag? = null

    @SuppressLint("SetTextI18n")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_read_nfc)
        Log.w(logTag, "ReadNfc Created")

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
            readNfc(tagFromIntent!!)
        } else {
            Log.d(logTag, "No NFC_TAG received")
            Toast.makeText(this, "Please tap an NFC tag", Toast.LENGTH_SHORT).show()
        }
    }

    private fun readNfc(tag: Tag) {
        Log.d(logTag, "Reading NFC tag...")
        val ndef = Ndef.get(tag)
        if (ndef != null) {
            ndef.connect()
            val ndefMessage = ndef.ndefMessage
            if (ndefMessage != null && ndefMessage.records.isNotEmpty()) {
                val record = ndefMessage.records[0]
                val payload = record.payload
                var nfcText = String(payload)

                nfcText = nfcText.replace("fr", "")

                Log.d(logTag, "NFC data read successfully: $nfcText")

                Toast.makeText(this, "NFC data read successfully", Toast.LENGTH_SHORT).show()

                Log.i(logTag, "Starting HomeActivity...")
                val intent = Intent(this, HomeActivity::class.java)
                intent.putExtra("NFC_TEXT", nfcText)
                startActivity(intent)
            } else {
                Log.d(logTag, "No data found on NFC tag")
                Toast.makeText(this, "No data found on NFC tag", Toast.LENGTH_SHORT).show()
            }
            ndef.close()
        } else {
            Log.d(logTag, "NFC Tag does not support NDEF")
            Toast.makeText(this, "NFC Tag does not support NDEF", Toast.LENGTH_SHORT).show()
        }
    }
}
