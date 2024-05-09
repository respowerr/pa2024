package com.example.applicationbenevoles


import android.annotation.SuppressLint
import android.os.Bundle
import android.util.Log
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.Request
import com.android.volley.RequestQueue
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley

class ProfileActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var usernameTextView: TextView
    private lateinit var emailTextView: TextView
    private lateinit var phoneTextView: TextView
    private lateinit var nameTextView: TextView
    private lateinit var lastNameTextView: TextView
    private lateinit var locationTextView: TextView
    private lateinit var passwordTextView: TextView

    private lateinit var queue: RequestQueue

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_profile)

        Log.d(logTag, "onCreate: ProfileActivity created")

        usernameTextView = findViewById(R.id.username_text_view)
        nameTextView = findViewById(R.id.name_text_view)
        lastNameTextView = findViewById(R.id.last_name_text_view)
        emailTextView = findViewById(R.id.email_text_view)
        passwordTextView = findViewById(R.id.password_text_view)
        phoneTextView = findViewById(R.id.phone_text_view)
        locationTextView = findViewById(R.id.location_text_view)
        queue = Volley.newRequestQueue(this)

        loadUserProfile()
    }

    private fun loadUserProfile() {
        val accessToken = intent.getStringExtra("access_token")
        val userId = intent.getIntExtra("user_id", 0)
        val url = getString(R.string.server_url_info) + "/$userId"

        Log.d(logTag, "loadUserProfile: URL: $url")

        val jsonObjectRequest = @SuppressLint("SetTextI18n")
        object : JsonObjectRequest(
            Request.Method.GET, url, null,
            Response.Listener { response ->
                Log.d(logTag, "loadUserProfile: Response JSON: $response")
                try {
                    val username = response.getString("username")
                    val password = response.getString("password")
                    val email = response.getString("email")
                    val phone = response.getString("phone")
                    val name = response.getString("name")
                    val lastName = response.getString("lastName")
                    val location = response.getString("location")

                    usernameTextView.text = "Username: $username"
                    emailTextView.text = "Email: $email"
                    nameTextView.text = "Name: $name"
                    lastNameTextView.text = "Last Name: $lastName"
                    phoneTextView.text = "Phone: $phone"
                    passwordTextView.text = "Password: $password"
                    locationTextView.text = "Location: $location"

                } catch (e: Exception) {
                    Log.e(logTag, "Error parsing user profile JSON: ${e.message}")
                }
            },
            Response.ErrorListener { error ->
                Log.e(logTag, "Error loading user profile: ${error.message}")
                Toast.makeText(this, "Error loading user profile", Toast.LENGTH_SHORT).show()
            }
        ) {
            override fun getHeaders(): MutableMap<String, String> {
                val headers = HashMap<String, String>()
                headers["Authorization"] = "Bearer $accessToken"
                return headers
            }
        }

        queue.add(jsonObjectRequest)
    }
}
