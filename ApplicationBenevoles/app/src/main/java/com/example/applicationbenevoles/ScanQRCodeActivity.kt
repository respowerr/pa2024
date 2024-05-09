package com.example.applicationbenevoles

import android.annotation.SuppressLint
import android.content.Intent
import android.os.Bundle
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.annotation.Nullable
import com.google.zxing.integration.android.IntentIntegrator
import com.google.zxing.integration.android.IntentResult

class ScanQRCodeActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_scan_qrcode)

        IntentIntegrator(this).initiateScan()
    }

    @SuppressLint("SetTextI18n")
    override fun onActivityResult(requestCode: Int, resultCode: Int, @Nullable data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)

        val result: IntentResult? = IntentIntegrator.parseActivityResult(requestCode, resultCode, data)
        if (result != null) {
            val textView: TextView = findViewById(R.id.qr_code_result)
            if (result.contents == null) {
                textView.text = "La lecture du QR code a échoué"
            } else {
                textView.text = "Résultat du QR code : ${result.contents}"
            }
        } else {
            super.onActivityResult(requestCode, resultCode, data)
        }
    }
}
