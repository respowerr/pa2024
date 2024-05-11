package com.example.applicationbenevoles

import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.Request
import com.android.volley.RequestQueue
import com.android.volley.toolbox.JsonArrayRequest
import com.android.volley.toolbox.StringRequest
import com.android.volley.toolbox.Volley
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Date
import java.util.Locale

class PlanningActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var planningTextView: TextView
    private lateinit var dateTextView: TextView
    private lateinit var queue: RequestQueue
    private lateinit var formattedDate: String
    private lateinit var previousDayButton: Button
    private lateinit var nextDayButton: Button
    private lateinit var joinedStatusTextView: TextView
    private var userId: Int = 0
    private lateinit var accessToken: String
    private var eventIdToCheck: String? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        Log.d(logTag, "onCreate: Planning created")

        planningTextView = findViewById(R.id.planningTextView)
        dateTextView = findViewById(R.id.dateTextView)
        previousDayButton = findViewById(R.id.previousDayButton)
        nextDayButton = findViewById(R.id.nextDayButton)
        joinedStatusTextView = findViewById(R.id.joinedStatusTextView)
        queue = Volley.newRequestQueue(this)

        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        formattedDate = dateFormat.format(Date())
        displayCurrentDate()

        userId = intent.getIntExtra("user_id", 0)
        accessToken = intent.getStringExtra("access_token") ?: ""

        if (accessToken.isNotEmpty()) {
            Log.d(logTag, "onCreate: Access token received: $accessToken")
            fetchPlanningData(userId.toString())
        } else {
            Log.e(logTag, "onCreate: Access token is empty or null")
            Toast.makeText(
                this@PlanningActivity,
                "Erreur: Access token is empty or null",
                Toast.LENGTH_SHORT
            ).show()
        }

        previousDayButton.setOnClickListener {
            updateDate(-1)
        }

        nextDayButton.setOnClickListener {
            updateDate(1)
        }
    }

    private fun updateDate(daysToAdd: Int) {
        val calendar = Calendar.getInstance()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        try {
            val currentDate = dateFormat.parse(formattedDate)
            if (currentDate != null) {
                calendar.time = currentDate
            }
            calendar.add(Calendar.DAY_OF_YEAR, daysToAdd)
            val newDate = calendar.time
            formattedDate = dateFormat.format(newDate)
            Log.d(logTag, "New Date: $formattedDate")
            displayCurrentDate()
            fetchPlanningData(userId.toString())
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    private fun displayCurrentDate() {
        dateTextView.text = "Date: $formattedDate"
    }

    private fun fetchPlanningData(userId: String) {
        Log.d(logTag, "fetchPlanningData: Fetching planning data for date: $formattedDate")

        val url = resources.getString(R.string.server_url_activity) + "?date=$formattedDate"
        Log.d(logTag, "fetchPlanningData: URL: $url")

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
    }

    private fun parsePlanningResponse(response: JSONArray): List<Event> {
        val planningList: MutableList<Event> = mutableListOf()
        val eventIdList = mutableListOf<String>()

        try {
            for (i in 0 until response.length()) {
                val event = response.getJSONObject(i)
                val eventDate = event.getString("eventStart")

                if (eventDate == formattedDate) {
                    val eventId = event.getString("id")
                    val eventName = event.getString("eventName")
                    val eventType = event.getString("eventType")
                    val eventStart = event.getString("eventStart")
                    val eventEnd = event.getString("eventEnd")
                    val location = event.getString("location")
                    val description = event.getString("description")
                    val eventDetails = Event(
                        eventId,
                        eventName,
                        eventType,
                        eventStart,
                        eventEnd,
                        location,
                        description
                    )
                    planningList.add(eventDetails)
                    Log.d(logTag, "Event parsed: $eventDetails")

                    eventIdList.add(eventId)

                    eventIdToCheck = eventId
                    Log.i(logTag, "$eventIdToCheck")
                }
            }
        } catch (e: JSONException) {
            Log.e(logTag, "Erreur de la récupération du JSON:" + e.stackTrace.contentToString())
        }
        if (planningList.isEmpty()) {
            eventIdToCheck = "0"
            Log.i(logTag, "eventIdToCheck set to 0 as there are no events")
        }

        return planningList
    }

    private fun displayPlanning(planningList: List<Event>) {
        val stringBuilder = StringBuilder()
        for (event in planningList) {
            stringBuilder.append(event.toString()).append("\n\n")
        }
        planningTextView.text = stringBuilder.toString()

        if (eventIdToCheck != null) {
            fetchUserEvents(userId.toString())
        }
    }

    private fun fetchUserEvents(userId: String) {
        val url = "${resources.getString(R.string.server_url_info)}/$userId"
        Log.i(logTag, userId)
        Log.d(logTag, "fetchUserEvents: URL: $url")

        val stringRequest = object : StringRequest(Method.GET, url,
            { response ->
                try {
                    val jsonResponse = JSONObject(response)
                    val eventsArray = jsonResponse.getJSONArray("events")
                    val eventIdList = mutableListOf<String>()
                    for (i in 0 until eventsArray.length()) {
                        val eventId = eventsArray.getJSONObject(i).getString("event_id")
                        eventIdList.add(eventId)
                    }
                    checkUserEventRegistration(eventIdList)
                } catch (e: JSONException) {
                    Log.e(logTag, "Error parsing user events JSON: ${e.message}")
                }
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "fetchUserEvents: Error fetching user events: $errorMessage")
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

        queue.add(stringRequest)
    }


    private fun checkUserEventRegistration(eventIdList: List<String>) {
        Log.d(logTag, "eventIdToCheck: $eventIdToCheck")
        val joinedMessage = if (eventIdToCheck != null && eventIdList.contains(eventIdToCheck)) {
            "Vous êtes inscrit à cet événement"
        } else {
            "Vous n'êtes pas inscrit à cet événement"
        }
        Log.d(logTag, "Joined Message: $joinedMessage")

        joinedStatusTextView.text = joinedMessage
    }

    data class Event(
        val id: String,
        val name: String,
        val type: String,
        val start: String,
        val end: String,
        val location: String,
        val description: String
    ) {
        override fun toString(): String {
            return "Id: $id\n" +
                    "Nom: $name\n" +
                    "Type: $type\n" +
                    "Début: $start\n" +
                    "Fin: $end\n" +
                    "Lieu: $location\n" +
                    "Description: $description"
        }
    }
}
