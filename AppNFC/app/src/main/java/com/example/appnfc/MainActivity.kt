package com.example.appnfc

import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.nfc.NdefMessage
import android.nfc.NdefRecord
import android.nfc.NfcAdapter
import android.nfc.NfcManager
import android.nfc.Tag
import android.nfc.tech.Ndef
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import com.example.appnfc.HomeActivity

class MainActivity : AppCompatActivity() {

    val TAG = MainActivity::class.java.simpleName
    private var adapter: NfcAdapter? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        initNfcAdapter()

    }

    private fun initNfcAdapter() {
        val nfcManager = getSystemService(Context.NFC_SERVICE) as NfcManager
        adapter = nfcManager.defaultAdapter
    }

    private fun enableNfcForegroundDispatch() {
        try {
            val intent = Intent(this, javaClass).addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP)
            val nfcPendingIntent =
                PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_MUTABLE)
            adapter?.enableForegroundDispatch(this, nfcPendingIntent, null, null)
        } catch (ex: IllegalStateException) {
            Log.e(TAG, "Error enabling NFC foreground dispatch", ex)
        }
    }

    private fun disableNfcForegroundDispatch() {
        try {
            adapter?.disableForegroundDispatch(this)
        } catch (ex: IllegalStateException) {
            Log.e(TAG, "Error disabling NFC foreground dispatch", ex)
        }
    }

    override fun onResume() {
        if (checkNFCDeviceSupport()) {
            enableNfcForegroundDispatch()
        }
        super.onResume()
    }

    override fun onPause() {
        if (checkNFCDeviceSupport()) {
            disableNfcForegroundDispatch()
        }
        super.onPause()
    }

    /**
     * Check whether the device supports nfc or not!
     */
    private fun checkNFCDeviceSupport(): Boolean {
        if (adapter == null) {
            Log.e(TAG, "checkNFCDeviceSupport: NFC NOT supported on this device!")
            return false
        } else if (!adapter!!.isEnabled) {
            Log.e(TAG, "checkNFCDeviceSupport: NFC NOT Enabled!")
            return false
        }
        return true
    }

    override fun onNewIntent(intent: Intent?) {
        super.onNewIntent(intent)

        Log.e("Main", "onNewIntent: ${intent?.dataString}")
        val tagFromIntent: Tag? = intent?.getParcelableExtra(NfcAdapter.EXTRA_TAG)
        launchYourApp(tagFromIntent)


    }

    private fun createNdefMessage(nfcText: String): NdefMessage {
        val ndefRecord = NdefRecord.createTextRecord(null, nfcText)
        return NdefMessage(arrayOf(ndefRecord))
    }


    private fun launchYourApp(tag: Tag?) {
        val ndef = Ndef.get(tag)
        var nfcText =""
        if (ndef != null) {
            try {
                ndef.connect()
                nfcText = "Hello"
                ndef.writeNdefMessage(createNdefMessage(nfcText))
                Toast.makeText(this, "NFC Tag written successfully", Toast.LENGTH_SHORT).show()
            } catch (e: Exception) {
                Toast.makeText(this, "Error writing to NFC Tag", Toast.LENGTH_SHORT).show()
            } finally {
                ndef.close()
            }
        } else {
            Toast.makeText(this, "NFC Tag does not support NDEF", Toast.LENGTH_SHORT).show()
        }

        val newIntent = Intent(this, HomeActivity::class.java)
        val nfcTagId: String? = tag?.id?.toString()
        newIntent.putExtra("NFC_TEXT", nfcText)
        startActivity(newIntent)
    }



}