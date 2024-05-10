package com.example.applicationbenevoles

import android.content.Intent
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
import org.json.JSONObject

data class Event(val id: Int, val details: String)

class ActivitiesActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var activityTextView: TextView
    private lateinit var queue: RequestQueue
    private lateinit var accessToken: String
    private lateinit var username: String
    private lateinit var password: String

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_activities)

        Log.d(logTag, "onCreate: Activity created")

        activityTextView = findViewById(R.id.activityText)

        queue = Volley.newRequestQueue(this)

        accessToken = intent.getStringExtra("access_token").toString()
        username = intent.getStringExtra("username").toString()
        password = intent.getStringExtra("password").toString()

        if (accessToken.isNotBlank()) {
            Log.d(logTag, "onCreate: Access token received: $accessToken")
            fetchActivityData(accessToken)
        } else {
            Log.e(logTag, "onCreate: Access token is empty or null")
            Toast.makeText(
                this@ActivitiesActivity,
                "Erreur: Jeton d'accès non disponible",
                Toast.LENGTH_SHORT
            ).show()
            redirectToMainActivity()
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


    private fun parseActivityResponse(response: JSONArray): List<Event> {
        val activityList = mutableListOf<Event>()
        try {
            for (i in 0 until response.length()) {
                val event = response.getJSONObject(i)
                val eventId = event.getInt("id")
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
                activityList.add(Event(eventId, eventDetails))
            }
        } catch (e: JSONException) {
            Log.e(logTag, "Erreur de la récupération du JSON: ${e.stackTraceToString()}")
        }
        return activityList
    }


    private fun displayActivity(activityList: List<Event>) {
        val activityContainer = findViewById<LinearLayout>(R.id.activityContainer)
        activityContainer.removeAllViews()

        for (event in activityList) {
            val activityView = TextView(this)
            activityView.text = event.details
            activityContainer.addView(activityView)

            val eventId = event.id

            val joinButton = Button(this)
            joinButton.text = "Rejoindre"
            joinButton.setOnClickListener {
                joinEvent(eventId)
            }
            activityContainer.addView(joinButton)

            val quitButton = Button(this)
            quitButton.text = "Quitter"
            quitButton.setOnClickListener {
                quitEvent(eventId)
            }
            activityContainer.addView(quitButton)
        }
    }

    private fun redirectToMainActivity() {
        val intent = Intent(this, MainActivity::class.java)
        startActivity(intent)
        finish()
    }

    private fun joinEvent(eventId: Int) {
        val url = "${resources.getString(R.string.server_url_activity)}/$eventId/join"

        val jsonObject = JSONObject().apply {
            put("username", username)
        }
        Log.w(logTag, "$jsonObject")

        val jsonObjectRequest = object : JsonObjectRequest(Method.POST, url, jsonObject,
            { response ->
                Toast.makeText(
                    this@ActivitiesActivity,
                    response.getString("message"),
                    Toast.LENGTH_SHORT
                ).show()
                fetchActivityData(accessToken)
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "joinEvent: Error joining event: $errorMessage")
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


    private fun quitEvent(eventId: Int) {
        val url = "${resources.getString(R.string.server_url_activity)}/$eventId/quit"

        val jsonObject = JSONObject().apply {
            put("username", username)
        }
        Log.w(logTag, "$jsonObject")

        val jsonObjectRequest = object : JsonObjectRequest(Method.DELETE, url, null,
            { response ->
                Toast.makeText(
                    this@ActivitiesActivity,
                    response.getString("message"),
                    Toast.LENGTH_SHORT
                ).show()
                fetchActivityData(accessToken)
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "joinEvent: Error joining event: $errorMessage")
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
