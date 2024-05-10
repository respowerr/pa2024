package com.example.applicationbenevoles

import android.graphics.Bitmap
import android.graphics.Color
import android.os.Bundle
import android.util.Log
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.Spinner
import androidx.appcompat.app.AppCompatActivity

import com.google.zxing.BarcodeFormat
import com.google.zxing.WriterException
import com.google.zxing.common.BitMatrix
import com.google.zxing.qrcode.QRCodeWriter
import com.android.volley.RequestQueue
import com.android.volley.toolbox.JsonArrayRequest
import com.android.volley.toolbox.Volley
import org.json.JSONArray
import org.json.JSONException

class GenerateQRCodeActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"

    private lateinit var editText: EditText
    private lateinit var spinner: Spinner
    private lateinit var queue: RequestQueue
    private lateinit var accessToken: String
    private lateinit var imageView: ImageView


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_generate_qrcode)

        editText = findViewById(R.id.editText)
        spinner = findViewById(R.id.spinner)
        imageView = findViewById(R.id.qr_code_image)
        queue = Volley.newRequestQueue(this)

        accessToken = intent.getStringExtra("access_token").toString()
        Log.d(logTag, "Access token received: $accessToken")

        fetchWarehouseDataAndGenerateQRCode()

        val generateButton = findViewById<Button>(R.id.generateButton)
        generateButton.setOnClickListener {
            val selectedWarehouse = spinner.selectedItem.toString()
            generateQRCode(selectedWarehouse)

        }
    }

    private fun fetchWarehouseDataAndGenerateQRCode() {
        val url = resources.getString(R.string.server_url_warehouse)
        Log.d(logTag, "Fetching warehouse data from: $url")

        val jsonArrayRequest = object : JsonArrayRequest(
            Method.GET, url, null,
            { response ->
                Log.d(logTag, "Response received: $response")
                val warehouseList = parseWarehouseData(response)
                setupSpinner(warehouseList)
            },
            { error ->
                Log.e(logTag, "Error fetching warehouse data: ${error.message}")
            }) {
            override fun getHeaders(): MutableMap<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }

        queue.add(jsonArrayRequest)
    }

    private fun parseWarehouseData(response: JSONArray): List<String> {
        val warehouseList = mutableListOf<String>()
        for (i in 0 until response.length()) {
            try {
                val warehouse = response.getJSONObject(i)
                val warehouseLocation = warehouse.getString("location")
                warehouseList.add(warehouseLocation)
            } catch (e: JSONException) {
                Log.e(logTag, "Error parsing warehouse data: ${e.message}")
            }
        }
        Log.d(logTag, "Parsed warehouse data: $warehouseList")
        return warehouseList
    }

    private fun setupSpinner(warehouseList: List<String>) {
        Log.d(logTag, "Setting up spinner with data: $warehouseList")
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, warehouseList)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinner.adapter = adapter
    }

    private fun generateQRCode(data: String) {
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
