package com.example.appnfc

import android.os.Bundle
import android.widget.Button
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import com.example.appnfc.R

class HomeActivity : AppCompatActivity() {

    private var nfcTagId: String? = null

    private lateinit var tagTextView: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_home)

        nfcTagId = intent.getStringExtra("NFC_TAG_ID")
        val nfcText = intent.getStringExtra("NFC_TEXT")

        tagTextView = findViewById(R.id.tagtext)

        updateTagText(nfcText)
    }

    private fun updateTagText(nfcText: String?) {
        nfcText?.let {
            tagTextView.text = "NFC Text: $nfcText"
        }
    }



}
