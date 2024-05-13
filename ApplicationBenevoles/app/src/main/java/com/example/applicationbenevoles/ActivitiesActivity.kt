package com.example.applicationbenevoles

import android.annotation.SuppressLint
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
import com.android.volley.toolbox.StringRequest
import com.android.volley.toolbox.Volley
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject
import java.text.SimpleDateFormat
import java.util.*

data class Event(val id: Int, val details: String, val eventEnd: String)

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
                val futureActivities = filterFutureActivities(activityList)
                displayActivity(futureActivities)
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
                val eventStart = event.getString("eventStartFormattedDate")
                val eventEnd = event.getString("eventEndFormattedDate")
                val location = event.getString("location")
                val description = event.getString("description")
                val eventDetails = "Nom: $eventName\n" +
                        "Type: $eventType\n" +
                        "Début: $eventStart\n" +
                        "Fin: $eventEnd\n" +
                        "Lieu: $location\n" +
                        "Description: $description\n\n"
                activityList.add(Event(eventId, eventDetails, eventEnd))
            }
        } catch (e: JSONException) {
            Log.e(logTag, "Erreur de la récupération du JSON: ${e.stackTraceToString()}")
        }
        return activityList
    }

    @SuppressLint("SetTextI18n")
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
        val intent = Intent(this@ActivitiesActivity, MainActivity::class.java)
        startActivity(intent)
    }

    private fun joinEvent(eventId: Int) {
        Log.d(logTag, "joinEvent: Attempting to join event with ID: $eventId")
        val joinUrl = "${resources.getString(R.string.server_url_activity)}/$eventId/join"

        val url = "${resources.getString(R.string.server_url_info)}/me"
        Log.d(logTag, "joinEvent: URL: $url")

        val stringRequest = object : StringRequest(Method.GET, url,
            { response ->
                try {
                    val jsonResponse = JSONObject(response)
                    val eventsArray = jsonResponse.getJSONArray("events")
                    val eventIdList = mutableListOf<Int>()
                    for (i in 0 until eventsArray.length()) {
                        val event = eventsArray.getJSONObject(i)
                        val eventId = event.getInt("id")
                        eventIdList.add(eventId)
                    }

                    if (eventIdList.contains(eventId)) {
                        val errorMessage = "Vous êtes déjà inscrit à cette activité"
                        Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
                        Log.e(logTag, "joinEvent: Error joining event: $errorMessage")
                    } else {
                        Log.d(logTag, "joinEvent: User is not already registered for this event, proceeding to join.")
                        val joinRequest = createJoinRequest(joinUrl)
                        queue.add(joinRequest)
                    }

                } catch (e: JSONException) {
                    Log.e(logTag, "Error parsing user events JSON: ${e.message}")
                }
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Log.e(logTag, "joinEvent: Error fetching user events: $errorMessage")
                Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }

        queue.add(stringRequest)
    }

    private fun createJoinRequest(joinUrl: String): StringRequest {
        return object : StringRequest(
            Method.POST, joinUrl,
            { _ ->
                Toast.makeText(
                    this@ActivitiesActivity,
                    "You joined the event successfully",
                    Toast.LENGTH_SHORT
                ).show()
                Log.d(logTag, "Successfully joined the event")
                redirectToMainActivity()
            },
            { _ ->
                val errorMessage = "Vous avez déjà une activité prévue en meme temps "
                Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
                Log.e(logTag, "Error joining event: $errorMessage")
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }
    }

    private fun quitEvent(eventId: Int) {
        val quitUrl = "${resources.getString(R.string.server_url_activity)}/$eventId/quit"

        val stringRequest = object : StringRequest(
            Method.DELETE, quitUrl,
            { _ ->
                Toast.makeText(
                    this@ActivitiesActivity,
                    "You left the event successfully",
                    Toast.LENGTH_SHORT
                ).show()
                Log.d(logTag, "quitEvent: Successfully left event with ID: $eventId")
                redirectToMainActivity()
            },
            { error ->
                val errorMessage = "Error: " + error.message
                Toast.makeText(this@ActivitiesActivity, errorMessage, Toast.LENGTH_SHORT).show()
                Log.e(logTag, "quitEvent: Error leaving event: $errorMessage")
            }
        ) {
            override fun getHeaders(): Map<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }
        queue.add(stringRequest)
    }

    private fun filterFutureActivities(activityList: List<Event>): List<Event> {
        val currentDate = Calendar.getInstance().time
        val filteredList = mutableListOf<Event>()
        for (event in activityList) {
            val eventDate = parseDate(event.eventEnd)
            if (eventDate != null && eventDate.after(currentDate)) {
                filteredList.add(event)
            }
        }
        return filteredList
    }

    private fun parseDate(dateString: String): Date? {
        val format = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        return try {
            format.parse(dateString)
        } catch (e: Exception) {
            Log.e(logTag, "Error parsing date: ${e.message}")
            null
        }
    }
}
