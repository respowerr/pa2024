package com.example.applicationbenevoles

import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var sharedPreferences: SharedPreferences
    private lateinit var loginButton: Button
    private var isLoggedIn = false
    private var accessToken: String? = null
    private var username: String? = null
    private var password: String? = null
    private var userId: Int = 0
    private var hasJoined: Boolean = false


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        sharedPreferences = getSharedPreferences("user_session", MODE_PRIVATE)

        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        accessToken = sharedPreferences.getString("access_token", null)
        username = sharedPreferences.getString("username", null)
        password = sharedPreferences.getString("password", null)
        userId = sharedPreferences.getInt("user_id", 0)
        hasJoined = sharedPreferences.getBoolean("hasJoined", false)

        loginButton = findViewById(R.id.loginButton)
        val planningButton: Button = findViewById(R.id.planningButton)
        val profileButton: Button = findViewById(R.id.profileButton)
        val activitiesButton: Button = findViewById(R.id.activitiesButton)
        val genQrCodeButton: Button = findViewById(R.id.genQrCodeButton)
        val scanQrCodeButton: Button = findViewById(R.id.scanQrCodeButton)
        val readNfcButton: Button = findViewById(R.id.readNfcButton)
        val writeNfcButton: Button = findViewById(R.id.writeNfcButton)

        updateLoginButton()

        loginButton.setOnClickListener {
            if (isLoggedIn) {
                logoutUser()
            } else {
                startActivity(Intent(this@MainActivity, LoginActivity::class.java))
            }
        }

        activitiesButton.setOnClickListener {
            if (accessToken != null) {
                val intent = Intent(this@MainActivity, ActivitiesActivity::class.java)
                intent.putExtra("username", username)
                intent.putExtra("password", password)
                intent.putExtra("access_token", accessToken)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder aux activités.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        profileButton.setOnClickListener {
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, ProfileActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder au profil.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        planningButton.setOnClickListener {
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, PlanningActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder au planning.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        readNfcButton.setOnClickListener {
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, ReadNfcActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder a la lecture du NFC.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        writeNfcButton.setOnClickListener {
            var userRole = intent.getStringExtra("user_role") ?: "ROLE_USER"
            userRole = userRole.replace("[", "").replace("]", "").replace("\"", "")

            if (userRole.equals("ROLE_ADMIN", ignoreCase = true)) {
                val intent = Intent(this@MainActivity, WriteNfcActivity::class.java)
                intent.putExtra("user_role", userRole)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez être administrateur pour accéder à cette fonctionnalité.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        scanQrCodeButton.setOnClickListener {
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, ScanQRCodeActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder au Scan du QrCode.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

        genQrCodeButton.setOnClickListener {
            var userRole = intent.getStringExtra("user_role") ?: "ROLE_USER"
            userRole = userRole.replace("[", "").replace("]", "").replace("\"", "")

            if (userRole.equals("ROLE_ADMIN", ignoreCase = true)) {
                val intent = Intent(this@MainActivity, GenerateQRCodeActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_role", userRole)
                startActivity(intent)
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez être administrateur pour accéder à cette fonctionnalité.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }
    }

    override fun onStart() {
        super.onStart()
        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        accessToken = sharedPreferences.getString("access_token", null)
        username = sharedPreferences.getString("username", null)
        password = sharedPreferences.getString("password", null)
        userId = sharedPreferences.getInt("user_id", 0)
        updateLoginButton()
    }


    private fun updateLoginButton() {
        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        loginButton.text = if (isLoggedIn) "Se déconnecter" else "Se connecter"
    }

    private fun logoutUser() {
        sharedPreferences.edit().apply {
            remove("is_logged_in")
            remove("access_token")
            remove("username")
            remove("password")
            remove("user_id")
            remove("hasJoined")
            apply()
        }
        updateLoginButton()
    }
}
