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

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        sharedPreferences = getSharedPreferences("user_session", MODE_PRIVATE)
        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        val userRole = sharedPreferences.getString("user_role", "")

        loginButton = findViewById(R.id.loginButton)
        val planningButton: Button = findViewById(R.id.planningButton)
        val profileButton: Button = findViewById(R.id.profileButton)
        val activitiesButton: Button = findViewById(R.id.activitiesButton)
        val nfcButton: Button = findViewById(R.id.nfcButton)
        val genQrCodeButton: Button = findViewById(R.id.genQrCodeButton)
        val scanQrCodeButton: Button = findViewById(R.id.scanQrCodeButton)


        updateLoginButton(isLoggedIn)

        loginButton.setOnClickListener {
            if (isLoggedIn) {
                logoutUser()
            } else {
                startActivity(Intent(this@MainActivity, LoginActivity::class.java))
            }
        }

        val accessToken = intent.getStringExtra("access_token")
        if (accessToken != null) {
            Log.d(logTag, accessToken)
        } else {
            Log.d(logTag, "Access token non trouvé dans l'intent")
        }

        activitiesButton.setOnClickListener {
            val accessToken = intent.getStringExtra("access_token")
            if (accessToken != null) {
                val intent = Intent(this@MainActivity, ActivitiesActivity::class.java)
                intent.putExtra("access_token", accessToken)
                Log.d(logTag, accessToken)
                startActivity(intent)
            } else {
                Toast.makeText(this@MainActivity, "Vous devez vous connecter pour accéder au planning.", Toast.LENGTH_SHORT).show()
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
                Toast.makeText(this@MainActivity, "Vous devez vous connecter pour accéder au profil.", Toast.LENGTH_SHORT).show()
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
                Toast.makeText(this@MainActivity, "Vous devez vous connecter pour accéder au profil.", Toast.LENGTH_SHORT).show()
            }
        }

        nfcButton.setOnClickListener{
            val intent = Intent(this@MainActivity, NfcActivity::class.java)
            intent.putExtra("user_role", userRole)
            startActivity(intent)
        }

        scanQrCodeButton.setOnClickListener{
            startActivity(Intent(this@MainActivity, ScanQRCodeActivity::class.java))
        }

        genQrCodeButton.setOnClickListener{
            if (userRole == "ROLE_ADMIN") {
                val intent = Intent(this@MainActivity, GenerateQRCodeActivity::class.java)
                intent.putExtra("user_role", userRole)
                startActivity(intent)
            } else {
                Toast.makeText(this@MainActivity, "Vous devez être administrateur pour accéder à cette fonctionnalité.", Toast.LENGTH_SHORT).show()
            }
        }

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
