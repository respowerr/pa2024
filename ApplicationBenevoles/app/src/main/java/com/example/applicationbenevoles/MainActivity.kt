package com.example.applicationbenevoles

import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.util.Log
import android.view.View
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
    private var userRole: String? = null

    private lateinit var planningButton: Button
    private lateinit var profileButton: Button
    private lateinit var activitiesButton: Button
    private lateinit var genQrCodeButton: Button
    private lateinit var scanQrCodeButton: Button
    private lateinit var writeNfcButton: Button
    private lateinit var readNfcButton: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        sharedPreferences = getSharedPreferences("user_session", MODE_PRIVATE)

        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        accessToken = sharedPreferences.getString("access_token", null)
        username = sharedPreferences.getString("username", null)
        password = sharedPreferences.getString("password", null)
        userId = sharedPreferences.getInt("user_id",0)
        userRole = sharedPreferences.getString("user_role", "ROLE_USER") ?: "ROLE_USER"
        Log.i(logTag, "$userRole")
        Log.i(logTag, "$username")

        loginButton = findViewById(R.id.loginButton)
        planningButton = findViewById(R.id.planningButton)
        profileButton = findViewById(R.id.profileButton)
        activitiesButton = findViewById(R.id.activitiesButton)
        genQrCodeButton = findViewById(R.id.genQrCodeButton)
        scanQrCodeButton = findViewById(R.id.scanQrCodeButton)
        readNfcButton = findViewById(R.id.readNfcButton)
        writeNfcButton = findViewById(R.id.writeNfcButton)

        updateLoginButton()
        updateViewVisibility()

        loginButton.setOnClickListener {
            if (isLoggedIn) {
                logoutUser()
            } else {
                startActivity(Intent(this@MainActivity, LoginActivity::class.java))
            }
        }

        activitiesButton.setOnClickListener {
            val userRole = sharedPreferences.getString("user_role", "ROLE_USER") ?: "ROLE_USER"

            if (accessToken != null) {
                val intent = Intent(this@MainActivity, ActivitiesActivity::class.java)
                intent.putExtra("username", username)
                intent.putExtra("password", password)
                intent.putExtra("access_token", accessToken)
                intent.putExtra("user_role", userRole)

                if (userRole == "ROLE_ADMIN" || userRole == "ROLE_USER") {
                    startActivity(intent)
                } else {
                    Toast.makeText(
                        this@MainActivity,
                        "Vous n'avez pas les autorisations nécessaires pour accéder aux activités.",
                        Toast.LENGTH_SHORT
                    ).show()
                }
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
                Log.i(logTag, "$userId")
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
            Log.d(logTag, "User role: $userRole")
            if (isLoggedIn) {
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
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder à cette fonctionnalité.",
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
            Log.d(logTag, "User role: $userRole")
            if (isLoggedIn) {
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
            } else {
                Toast.makeText(
                    this@MainActivity,
                    "Vous devez vous connecter pour accéder à cette fonctionnalité.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        }

    }

    private fun updateViewVisibility() {
        if (userRole.equals("[\"ROLE_ADMIN\"]", ignoreCase = true)) {
            genQrCodeButton.visibility = View.VISIBLE
            writeNfcButton.visibility = View.VISIBLE

        } else {
            genQrCodeButton.visibility = View.GONE
            writeNfcButton.visibility = View.GONE
            Log.i(logTag, "-----------$userRole")

        }
    }

    override fun onStart() {
        super.onStart()
        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false)
        accessToken = sharedPreferences.getString("access_token", null)
        username = sharedPreferences.getString("username", null)
        password = sharedPreferences.getString("password", null)
        userId = sharedPreferences.getInt("user_id", 0)
        userRole = sharedPreferences.getString("user_role", "ROLE_USER") ?: "ROLE_USER"
        Log.i(logTag, "!!!!$userRole")
        updateLoginButton()
        updateViewVisibility()

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
            remove("user_role")
            apply()
        }
        updateLoginButton()

    }
}
