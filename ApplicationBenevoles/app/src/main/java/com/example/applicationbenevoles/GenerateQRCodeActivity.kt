package com.example.applicationbenevoles

import android.graphics.Bitmap
import android.graphics.Color
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import androidx.appcompat.app.AppCompatActivity
import com.google.zxing.BarcodeFormat
import com.google.zxing.WriterException
import com.google.zxing.common.BitMatrix
import com.google.zxing.qrcode.QRCodeWriter

class GenerateQRCodeActivity : AppCompatActivity() {

    private lateinit var editText: EditText
    private lateinit var imageView: ImageView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_generate_qrcode)

        editText = findViewById(R.id.editText)
        imageView = findViewById(R.id.qr_code_image)

        val generateButton = findViewById<Button>(R.id.generateButton)
        generateButton.setOnClickListener {
            generateQRCode()
        }
    }

    private fun generateQRCode() {
        val data = editText.text.toString()
        val width = 512
        val height = 512

        val writer = QRCodeWriter()
        try {
            val bitMatrix: BitMatrix = writer.encode(data, BarcodeFormat.QR_CODE, width, height)
            val bitmap: Bitmap = Bitmap.createBitmap(width, height, Bitmap.Config.RGB_565)
            for (x in 0 until width) {
                for (y in 0 until height) {
                    bitmap.setPixel(x, y, if (bitMatrix.get(x, y)) Color.BLACK else Color.WHITE)
                }
            }
            imageView.setImageBitmap(bitmap)
        } catch (e: WriterException) {
            e.printStackTrace()
        }
    }
}
