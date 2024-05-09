package com.example.applicationbenevoles

import android.os.Bundle
import android.util.Log
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity

class HomeActivity : AppCompatActivity() {

    private val logTag = "NFC_APP"
    private lateinit var tagTextView: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        Log.d(logTag, "Create HomeActivity")
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_home)

        val nfcText = intent.getStringExtra("NFC_TEXT")

        Log.d(logTag, "Received NFC_TEXT: $nfcText")
        tagTextView = findViewById(R.id.tagtext)

        if (!nfcText.isNullOrEmpty()) {
            tagTextView.text = nfcText
            updateTagText(nfcText)
        } else {
            tagTextView.text = "Aucun texte NFC disponible"
        }
    }

    private fun updateTagText(nfcText: String?) {
        nfcText?.let {
            tagTextView.text = "NFC Text: $nfcText"
        }
    }
}
