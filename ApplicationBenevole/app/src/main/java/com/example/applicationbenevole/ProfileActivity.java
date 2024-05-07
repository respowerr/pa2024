package com.example.applicationbenevole;

import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;
import java.util.Objects;

public class ProfileActivity extends AppCompatActivity {

    private final String logTag = "APP_BENEVOLE";
    private TextView usernameTextView;
    private TextView emailTextView;
    private TextView phoneTextView;

    private TextView nameTextView;
    private TextView lastNameTextView;
    private TextView locationTextView;
    private TextView passwordTextView;
    private RequestQueue queue;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);

        Log.d(logTag, "onCreate: Activity created");

        usernameTextView = findViewById(R.id.username_text_view);
        emailTextView = findViewById(R.id.email_text_view);
        nameTextView = findViewById(R.id.name_text_view);
        lastNameTextView = findViewById(R.id.last_name_text_view);
        phoneTextView = findViewById(R.id.phone_text_view);
        locationTextView = findViewById(R.id.location_text_view);
        passwordTextView = findViewById(R.id.password_text_view);

        queue = Volley.newRequestQueue(this);

        String accessToken = getIntent().getStringExtra("access_token");
        int userId = getIntent().getIntExtra("user_id", 0);
        Log.d(logTag, "onCreate: User ID received: " + userId);
        if (accessToken != null && !accessToken.isEmpty() && userId != 0) {
            Log.d(logTag, "onCreate: Access token received: " + accessToken);
            fetchUserData(accessToken, userId);
        } else {
            Log.e(logTag, "onCreate: Access token or user ID is empty or invalid");
            Toast.makeText(ProfileActivity.this, "Erreur: Access token or user ID is empty or invalid", Toast.LENGTH_SHORT).show();
        }
    }


    private void fetchUserData(String accessToken, int userId) {
        Log.d(logTag, "fetchUserData: Fetching user data...");

        String url = getResources().getString(R.string.server_url_info) + userId;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.GET, url, null,
                response -> {
                    Log.d(logTag, "fetchUserData: User data fetched successfully");
                    displayUserData(response);
                },
                error -> {
                    String errorMessage = "Error: " + error.getMessage();
                    Log.e(logTag, "fetchUserData: Error fetching user data: " + errorMessage);
                    Toast.makeText(ProfileActivity.this, errorMessage, Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            public Map<String, String> getHeaders() {
                Map<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + accessToken);
                return headers;
            }
        };

        queue.add(jsonObjectRequest);
    }


    private void displayUserData(JSONObject userData) {
        try {
            String username = userData.getString("username");
            String email = userData.getString("email");
            String phone = userData.getString("phone");
            String name = userData.getString("name");
            String lastName = userData.getString("lastName");
            String location = userData.getString("location");
            String password = userData.getString("password");


            usernameTextView.setText(username);
            emailTextView.setText(email);
            nameTextView.setText(name);
            lastNameTextView.setText(lastName);
            phoneTextView.setText(phone);
            locationTextView.setText(location);
            passwordTextView.setText(password);

        } catch (JSONException e) {
            Log.e(logTag, "Erreur lors de l'affichage des données de l'utilisateur: " + e.getMessage());
        }
    }
}
