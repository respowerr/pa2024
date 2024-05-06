package com.example.appnfc

import android.os.Bundle
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity

class HomeActivity : AppCompatActivity() {

    private lateinit var tagTextView: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_home)

        val nfcText = intent.getStringExtra("NFC_TEXT")

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
