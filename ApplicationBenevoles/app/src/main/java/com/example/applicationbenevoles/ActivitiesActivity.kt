package com.example.applicationbenevoles

import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.Request
import com.android.volley.RequestQueue
import com.android.volley.toolbox.JsonArrayRequest
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import org.json.JSONArray
import org.json.JSONException

class ActivitiesActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var activityTextView: TextView
    private lateinit var queue: RequestQueue
    private lateinit var accessToken: String


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_activities)

        Log.d(logTag, "onCreate: Activity created")

        activityTextView = findViewById(R.id.activityText)

        queue = Volley.newRequestQueue(this)

        accessToken = intent.getStringExtra("access_token").toString()
        if (!accessToken.isNullOrBlank()) {
            Log.d(logTag, "onCreate: Access token received: $accessToken")
            fetchActivityData(accessToken)
        } else {
            Log.e(logTag, "onCreate: Access token is empty or null")
            Toast.makeText(
                this@ActivitiesActivity,
                "Erreur: Jeton d'accès non disponible",
                Toast.LENGTH_SHORT
            ).show()
        }
    }

    private fun fetchActivityData(accessToken: String) {
        Log.d(logTag, "fetchActivityData: Fetching activity data...")

        val url = resources.getString(R.string.server_url_activity)

        val jsonArrayRequest = object : JsonArrayRequest(Request.Method.GET, url, null,
            { response ->
                Log.d(logTag, "fetchActivityData: Activity data fetched successfully")
                val activityList = parseActivityResponse(response)
                displayActivity(activityList)
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "fetchActivityData: Error fetching activity data: $errorMessage")
                Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                Log.d(logTag, headers.toString())
                return headers
            }
        }

        queue.add(jsonArrayRequest)
    }

    private fun parseActivityResponse(response: JSONArray): List<String> {
        val activityList = mutableListOf<String>()
        try {
            for (i in 0 until response.length()) {
                val event = response.getJSONObject(i)
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
                        "Description: $description\n\n"
                activityList.add(eventDetails)
            }
        } catch (e: JSONException) {
            Log.e(logTag, "Erreur de la récuperation du JSON: ${e.stackTraceToString()}")
        }
        return activityList
    }

    private fun displayActivity(activityList: List<String>) {
        val activityContainer = findViewById<LinearLayout>(R.id.activityContainer)
        activityContainer.removeAllViews()

        for (activity in activityList) {
            val activityView = TextView(this)
            activityView.text = activity
            activityContainer.addView(activityView)

            val eventId = extractEventId(activity)
            val eventIdTextView = TextView(this)
            eventIdTextView.text = "ID de l'activité: $eventId"
            activityContainer.addView(eventIdTextView)

            val joinButton = Button(this)
            joinButton.text = "Rejoindre"
            joinButton.setOnClickListener {
                joinEvent(eventId, accessToken)
            }
            activityContainer.addView(joinButton)

            val quitButton = Button(this)
            quitButton.text = "Quitter"
            quitButton.setOnClickListener {
                quitEvent(eventId, accessToken)
            }
            activityContainer.addView(quitButton)
        }
    }



    private fun extractEventId(activity: String): Int {
        val regex = Regex("\\d+")
        val matchResult = regex.find(activity)
        val eventIdString = matchResult?.value
        Log.d(logTag, "extractEventId: Extracted event ID string: $eventIdString")
        return eventIdString?.toIntOrNull() ?: -1
    }


    private fun joinEvent(eventId: Int, accessToken: String) {
        Log.d(logTag, "joinEvent: Joining event with ID: $eventId")
        val url = resources.getString(R.string.server_url_activity) + "/" + eventId + "/join"

        val jsonObjectRequest = object : JsonObjectRequest(Method.POST, url, null,
            { _ ->
                Log.d(logTag, "joinEvent: Successfully joined event with ID: $eventId")
                Toast.makeText(this@ActivitiesActivity, "Inscription réussie à l'événement", Toast.LENGTH_SHORT).show()
            },
            { error ->
                val errorMessage = "Erreur lors de l'inscription à l'événement: " + error.message
                Log.e(logTag, "joinEvent: $errorMessage")
                Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }

        queue.add(jsonObjectRequest)
    }

    private fun quitEvent(eventId: Int, accessToken: String) {
        Log.d(logTag, "quitEvent: Quitting event with ID: $eventId")
        val url = resources.getString(R.string.server_url_activity) + "/" + eventId + "/quit"

        val jsonObjectRequest = object : JsonObjectRequest(Method.DELETE, url, null,
            { _ ->
                Log.d(logTag, "quitEvent: Successfully quit event with ID: $eventId")
                Toast.makeText(this@ActivitiesActivity, "Désinscription réussie de l'événement", Toast.LENGTH_SHORT).show()
            },
            { error ->
                val errorMessage = "Erreur lors de la désinscription de l'événement: " + error.message
                Log.e(logTag, "quitEvent: $errorMessage")
                Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }

        queue.add(jsonObjectRequest)
    }


}
