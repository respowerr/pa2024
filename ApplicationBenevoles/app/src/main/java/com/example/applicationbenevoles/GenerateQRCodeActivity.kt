package com.example.applicationbenevoles

import android.graphics.Bitmap
import android.graphics.Color
import android.os.Bundle
import android.util.Log
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.ImageView
import android.widget.Spinner
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.Request
import com.android.volley.RequestQueue
import com.android.volley.toolbox.JsonArrayRequest
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.google.zxing.BarcodeFormat
import com.google.zxing.WriterException
import com.google.zxing.common.BitMatrix
import com.google.zxing.qrcode.QRCodeWriter
import org.json.JSONArray
import org.json.JSONException

class GenerateQRCodeActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"

    private lateinit var spinner: Spinner
    private lateinit var queue: RequestQueue
    private lateinit var accessToken: String
    private lateinit var imageView: ImageView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_generate_qrcode)

        spinner = findViewById(R.id.spinner)
        imageView = findViewById(R.id.qr_code_image)
        queue = Volley.newRequestQueue(this)

        accessToken = intent.getStringExtra("access_token").toString()
        Log.d(logTag, "Access token received: $accessToken")

        fetchWarehouseDataAndGenerateQRCode()

        val generateButton = findViewById<Button>(R.id.generateButton)
        generateButton.setOnClickListener {
            val selectedWarehouse = spinner.selectedItem as Warehouse
            fetchWarehouseDetails(selectedWarehouse.id)
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

    private fun fetchWarehouseDetails(warehouseId: String) {
        val url = "${resources.getString(R.string.server_url_warehouse)}/$warehouseId"
        Log.w(logTag, url)
        val jsonObjectRequest = object : JsonObjectRequest(
            Request.Method.GET, url, null,
            { response ->
                try {
                    val warehouseId = response.getLong("warehouse_id")
                    val location = response.getString("location")
                    val rackCapacity = response.getInt("rack_capacity")
                    val utilization = response.getDouble("utilization")
                    val currentStock = response.getInt("current_stock")

                    val data =
                        "\nWarehouse ID: $warehouseId\nLocation: $location\nRack Capacity: $rackCapacity\nUtilization: $utilization\nCurrent Stock: $currentStock"
                    generateQRCode(data)
                } catch (e: JSONException) {
                    Log.e(logTag, "Error parsing warehouse details: ${e.message}")
                }
            },
            { error ->
                Log.e(logTag, "Error fetching warehouse details: ${error.message}")
            }) {
            override fun getHeaders(): MutableMap<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }

        queue.add(jsonObjectRequest)
    }

    private fun parseWarehouseData(response: JSONArray): List<Warehouse> {
        val warehouseList = mutableListOf<Warehouse>()
        for (i in 0 until response.length()) {
            try {
                val warehouse = response.getJSONObject(i)
                val warehouseId = warehouse.getString("warehouse_id")
                val location = warehouse.getString("location")
                warehouseList.add(Warehouse(warehouseId, location))
            } catch (e: JSONException) {
                Log.e(logTag, "Error parsing warehouse data: ${e.message}")
            }
        }
        Log.d(logTag, "Parsed warehouse data: $warehouseList")
        return warehouseList
    }

    private fun setupSpinner(warehouseList: List<Warehouse>) {
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

data class Warehouse(val id: String, val location: String)
