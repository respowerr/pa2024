package com.example.applicationbenevoles

import android.annotation.SuppressLint
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.Request
import com.android.volley.RequestQueue
import com.android.volley.toolbox.JsonArrayRequest
import com.android.volley.toolbox.Volley
import org.json.JSONArray
import org.json.JSONException
import java.text.SimpleDateFormat
import java.util.*

class PlanningActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var planningTextView: TextView
    private lateinit var dateTextView: TextView
    private lateinit var queue: RequestQueue
    private lateinit var formattedDate: String
    private lateinit var previousDayButton: Button
    private lateinit var nextDayButton: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        Log.d(logTag, "onCreate: Planning created")

        planningTextView = findViewById(R.id.planningTextView)
        dateTextView = findViewById(R.id.dateTextView)
        previousDayButton = findViewById(R.id.previousDayButton)
        nextDayButton = findViewById(R.id.nextDayButton)
        queue = Volley.newRequestQueue(this)

        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        formattedDate = dateFormat.format(Date())
        displayCurrentDate()

        val accessToken = intent.getStringExtra("access_token")
        if (!accessToken.isNullOrEmpty()) {
            Log.d(logTag, "onCreate: Access token received: $accessToken")
            fetchPlanningData(accessToken, formattedDate)
        } else {
            Log.e(logTag, "onCreate: Access token is empty or null")
            Toast.makeText(this@PlanningActivity, "Erreur: Access token is empty or null", Toast.LENGTH_SHORT).show()
        }

        previousDayButton.setOnClickListener {
            val calendar = Calendar.getInstance()
            try {
                val currentDate = dateFormat.parse(formattedDate)
                if (currentDate != null) {
                    calendar.time = currentDate
                }
                calendar.add(Calendar.DAY_OF_YEAR, -1)
                val previousDate = calendar.time

                formattedDate = dateFormat.format(previousDate)
                Log.d(logTag, "Previous Date: $formattedDate")
                fetchPlanningData(accessToken, formattedDate)
                displayCurrentDate()
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }

        nextDayButton.setOnClickListener {
            val calendar = Calendar.getInstance()
            try {
                val currentDate = dateFormat.parse(formattedDate)
                if (currentDate != null) {
                    calendar.time = currentDate
                }
                calendar.add(Calendar.DAY_OF_YEAR, 1)
                val nextDate = calendar.time

                formattedDate = dateFormat.format(nextDate)
                Log.d(logTag, "Next Date: $formattedDate")
                fetchPlanningData(accessToken, formattedDate)
                displayCurrentDate()
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }

    @SuppressLint("SetTextI18n")
    private fun displayCurrentDate() {
        dateTextView.text = "Date: $formattedDate"
    }

    private fun fetchPlanningData(accessToken: String?, formattedDate: String) {
        Log.d(logTag, "fetchPlanningData: Fetching planning data for date: $formattedDate")

        val url = resources.getString(R.string.server_url_activity) + "?date=$formattedDate"

        val jsonArrayRequest = object : JsonArrayRequest(Request.Method.GET, url, null,
            { response ->
                Log.d(logTag, "fetchPlanningData: Planning data fetched successfully")
                val planningList = parsePlanningResponse(response)
                displayPlanning(planningList)
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "fetchPlanningData: Error fetching planning data: $errorMessage")
                Toast.makeText(this@PlanningActivity, errorMessage, Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers: MutableMap<String, String> = HashMap()
                headers["Authorization"] = "Bearer $accessToken"
                Log.d(logTag, headers.toString())
                return headers
            }
        }

        queue.add(jsonArrayRequest)
        this.formattedDate = formattedDate
    }

    private fun parsePlanningResponse(response: JSONArray): List<String> {
        val planningList: MutableList<String> = ArrayList()
        try {
            for (i in 0 until response.length()) {
                val event = response.getJSONObject(i)
                val eventDate = event.getString("eventStart")

                if (eventDate == formattedDate) {
                    val eventName = event.getString("eventName")
                    val eventType = event.getString("eventType")
                    val eventStart = event.getString("eventStart")
                    val eventEnd = event.getString("eventEnd")
                    val location = event.getString("location")
                    val description = event.getString("description")
                    val eventDetails = "Nom: $eventName\n" +
                            "Type: $eventType\n" +
                            "Début: $eventStart\n" +
                            "Fin: $eventEnd\n" +
                            "Lieu: $location\n" +
                            "Description: $description"
                    planningList.add(eventDetails)
                }
            }
        } catch (e: JSONException) {
            Log.e(logTag, "Erreur de la récuperation du JSON:" + e.stackTrace.contentToString())
        }
        return planningList
    }

    @SuppressLint("SetTextI18n")
    private fun displayPlanning(planningList: List<String>) {
        if (planningList.isEmpty()) {
            planningTextView.text = "Aucun événement prévu pour ce jour"
        } else {
            val stringBuilder = StringBuilder()
            for (event in planningList) {
                stringBuilder.append(event).append("\n")
            }
            planningTextView.text = stringBuilder.toString()
        }
    }
}
