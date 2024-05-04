package com.example.applicationbenevole;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;
import java.nio.charset.StandardCharsets;
import java.util.Arrays;

public class LoginActivity extends AppCompatActivity {

    private final String logTag = "APP_BENEVOLE";
    private EditText usernameEditText;
    private EditText passwordEditText;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        usernameEditText = findViewById(R.id.usernameEditText);
        passwordEditText = findViewById(R.id.passwordEditText);
        Button loginButton = findViewById(R.id.loginButton);

        loginButton.setOnClickListener(v -> loginUser());
    }

    private void loginUser() {
        String username = usernameEditText.getText().toString().trim();
        String password = passwordEditText.getText().toString().trim();

        if (TextUtils.isEmpty(username)) {
            usernameEditText.setError("Entrez votre username");
            return;
        }

        if (TextUtils.isEmpty(password)) {
            passwordEditText.setError("Entrez votre mot de passe");
            return;
        }

        String url = getResources().getString(R.string.server_url_account);

        JSONObject jsonBody = new JSONObject();
        try {
            jsonBody.put("username", username);
            jsonBody.put("password", password);
        } catch (JSONException e) {
            Log.e(logTag, "Erreur de création du JSONBody:" + Arrays.toString(e.getStackTrace()));
            Toast.makeText(LoginActivity.this, "Erreur de création du JSONBody", Toast.LENGTH_SHORT).show();
            return;
        }

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.POST, url, jsonBody,
                response -> {
                    try {
                        Log.d(logTag, "response du serveur: " + response);
                        String accessToken = response.getString("accessToken");
                        if (!TextUtils.isEmpty(accessToken)) {
                            Log.i(logTag, "Connexion réussie");
                            saveUserSession();
                            openMainActivity(accessToken);
                            Toast.makeText(LoginActivity.this, "Connexion réussie", Toast.LENGTH_SHORT).show();
                            finish();
                        } else {
                            Toast.makeText(LoginActivity.this, "Erreur de login. Veuillez réessayer", Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(logTag, Arrays.toString(e.getStackTrace()));
                        Toast.makeText(LoginActivity.this, "Erreur Server", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    String errorMessage;
                    if (error.networkResponse != null && error.networkResponse.data != null) {
                        errorMessage = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                        Log.e(logTag, errorMessage);
                    } else {
                        errorMessage = "Error: " + error.getMessage();
                        Log.e(logTag, errorMessage);
                    }

                    Toast.makeText(LoginActivity.this, "Une erreur s'est produite lors de la connexion. Veuillez vérifier votre connexion Internet et réessayer.", Toast.LENGTH_SHORT).show();
                }

        );

        RequestQueue queue = Volley.newRequestQueue(this);
        queue.add(jsonObjectRequest);
    }

    private void saveUserSession() {
        SharedPreferences sharedPref = getSharedPreferences("user_session", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPref.edit();
        editor.putBoolean("is_logged_in", true);
        editor.apply();
    }

    private void openMainActivity(String accessToken) {
        Intent intent = new Intent(LoginActivity.this, MainActivity.class);
        intent.putExtra("access_token", accessToken);
        Log.w(logTag, String.valueOf(intent));
        startActivity(intent);
    }


}
