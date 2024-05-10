package com.example.applicationbenevoles

import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.text.TextUtils
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.Request
import com.android.volley.RequestQueue
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import org.json.JSONException
import org.json.JSONObject
import java.nio.charset.StandardCharsets

class LoginActivity : AppCompatActivity() {

    private val logTag = "APP_BENEVOLE"
    private lateinit var usernameEditText: EditText
    private lateinit var passwordEditText: EditText

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

        usernameEditText = findViewById(R.id.usernameEditText)
        passwordEditText = findViewById(R.id.passwordEditText)
        val loginButton: Button = findViewById(R.id.loginButton)

        loginButton.setOnClickListener { loginUser() }
    }

    private fun loginUser() {
        val username = usernameEditText.text.toString().trim()
        val password = passwordEditText.text.toString().trim()

        if (TextUtils.isEmpty(username)) {
            usernameEditText.error = "Entrez votre username"
            return
        }

        if (TextUtils.isEmpty(password)) {
            passwordEditText.error = "Entrez votre mot de passe"
            return
        }

        val url = resources.getString(R.string.server_url_account)

        val jsonBody = JSONObject()
        try {
            jsonBody.put("username", username)
            jsonBody.put("password", password)
        } catch (e: JSONException) {
            Log.e(logTag, "Erreur de création du JSONBody:" + e.stackTrace.contentToString())
            Toast.makeText(this@LoginActivity, "Erreur de création du JSONBody", Toast.LENGTH_SHORT)
                .show()
            return
        }

        val jsonObjectRequest = JsonObjectRequest(Request.Method.POST, url, jsonBody,
            { response ->
                try {
                    Log.d(logTag, "response du serveur: $response")
                    val accessToken = response.getString("accessToken")
                    val userId = response.getInt("id")
                    val userRole = response.getString("roles")
                    if (accessToken.isNotEmpty()) {
                        Log.i(logTag, "Connexion réussie")
                        saveUserSession()
                        openMainActivity(username, password, accessToken, userId, userRole)
                        Toast.makeText(this@LoginActivity, "Connexion réussie", Toast.LENGTH_SHORT)
                            .show()
                        finish()
                    } else {
                        Toast.makeText(
                            this@LoginActivity,
                            "Erreur de login. Veuillez réessayer",
                            Toast.LENGTH_SHORT
                        ).show()
                    }
                } catch (e: JSONException) {
                    Log.e(logTag, e.stackTrace.contentToString())
                    Toast.makeText(this@LoginActivity, "Erreur Server", Toast.LENGTH_SHORT).show()
                }
            },
            { error ->
                val errorMessage: String =
                    if (error.networkResponse != null && error.networkResponse.data != null) {
                        String(error.networkResponse.data, StandardCharsets.UTF_8)
                    } else {
                        "Error: " + error.message
                    }
                Log.e(logTag, errorMessage)
                Toast.makeText(
                    this@LoginActivity,
                    "Une erreur s'est produite lors de la connexion. Veuillez vérifier votre connexion Internet et réessayer.",
                    Toast.LENGTH_SHORT
                ).show()
            }
        )

        val queue: RequestQueue = Volley.newRequestQueue(this)
        queue.add(jsonObjectRequest)
    }

    private fun saveUserSession() {
        val sharedPref: SharedPreferences =
            getSharedPreferences("user_session", Context.MODE_PRIVATE)
        val editor: SharedPreferences.Editor = sharedPref.edit()
        editor.putBoolean("is_logged_in", true)
        editor.apply()
    }

    private fun openMainActivity(username: String, password: String, accessToken: String, userId: Int, userRole: String) {
        val intent = Intent(this@LoginActivity, MainActivity::class.java)
        intent.putExtra("username", username)
        intent.putExtra("password", password)
        intent.putExtra("access_token", accessToken)
        intent.putExtra("user_id", userId)
        intent.putExtra("user_role", userRole)
        Log.d(logTag, userRole)
        Log.w(logTag, intent.toString())
        startActivity(intent)
    }
}
