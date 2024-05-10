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

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        accessToken = savedInstanceState?.getString("access_token")
        username = savedInstanceState?.getString("username")
        password = savedInstanceState?.getString("password")

        sharedPreferences = getSharedPreferences("user_session", MODE_PRIVATE)
        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        val userRole = sharedPreferences.getString("user_role", "")
        if (userRole != null) {
            Log.i(logTag, userRole)
        }

        loginButton = findViewById(R.id.loginButton)
        val planningButton: Button = findViewById(R.id.planningButton)
        val profileButton: Button = findViewById(R.id.profileButton)
        val activitiesButton: Button = findViewById(R.id.activitiesButton)
        val genQrCodeButton: Button = findViewById(R.id.genQrCodeButton)
        val scanQrCodeButton: Button = findViewById(R.id.scanQrCodeButton)
        val readNfcButton: Button = findViewById(R.id.readNfcButton)
        val writeNfcButton: Button = findViewById(R.id.writeNfcButton)

        updateLoginButton(isLoggedIn)

        loginButton.setOnClickListener {
            if (isLoggedIn) {
                logoutUser()
            } else {
                startActivity(Intent(this@MainActivity, LoginActivity::class.java))
            }
        }


        activitiesButton.setOnClickListener {
            val username = intent.getStringExtra("username")
            val password = intent.getStringExtra("password")
            val accessToken = intent.getStringExtra("access_token")
            if (accessToken != null) {
                val intent = Intent(this@MainActivity, ActivitiesActivity::class.java)
                intent.putExtra("username", username)
                intent.putExtra("password", password)
                intent.putExtra("access_token", accessToken)
                if (username != null) {
                    Log.d(logTag, username)
                }
                if (password != null) {
                    Log.d(logTag, password)
                }
                Log.d(logTag, accessToken)
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
            val accessToken = intent.getStringExtra("access_token")
            val userId = intent.getIntExtra("user_id", 0)
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, ProfileActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                Log.w(logTag, userId.toString())
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
            val accessToken = intent.getStringExtra("access_token")
            val userId = intent.getIntExtra("user_id", 0)
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, PlanningActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                Log.w(logTag, userId.toString())
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
            val accessToken = intent.getStringExtra("access_token")
            val userId = intent.getIntExtra("user_id", 0)
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, ReadNfcActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                Log.w(logTag, userId.toString())
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

            Log.w(logTag, "UserRole: $userRole")
            if (userRole.equals("ROLE_ADMIN", ignoreCase = true)) {
                Log.d(logTag, userRole)
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
            val accessToken = intent.getStringExtra("access_token")
            val userId = intent.getIntExtra("user_id", 0)
            if (accessToken != null && userId != 0) {
                val intent = Intent(this@MainActivity, ScanQRCodeActivity::class.java)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_id", userId)
                Log.w(logTag, userId.toString())
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
            val accessToken = intent.getStringExtra("access_token")
            var userRole = intent.getStringExtra("user_role") ?: "ROLE_USER"
            userRole = userRole.replace("[", "").replace("]", "").replace("\"", "")

            Log.w(logTag, "UserRole: $userRole")
            if (userRole.equals("ROLE_ADMIN", ignoreCase = true)) {
                Log.d(logTag, userRole)
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
        updateLoginButton(isLoggedIn)
    }

    private fun updateLoginButton(isLoggedIn: Boolean) {
        loginButton.text = if (isLoggedIn) "Se déconnecter" else "Se connecter"
    }

    private fun logoutUser() {
        val editor: SharedPreferences.Editor = sharedPreferences.edit()
        editor.remove("is_logged_in")
        editor.remove("access_token")
        editor.remove("user_role")
        editor.apply()
        isLoggedIn = false
        updateLoginButton(isLoggedIn)
    }
}
