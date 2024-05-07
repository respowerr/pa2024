package com.example.applicationbenevole;

import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;

public class ActivitiesActivity extends AppCompatActivity {


    private final String logTag = "APP_BENEVOLE";
    private TextView activityTextView;
    private RequestQueue queue;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_activities);

        Log.d(logTag, "onCreate: Activity created");

        activityTextView = findViewById(R.id.activityTextView);

        // Initialise la RequestQueue
        queue = Volley.newRequestQueue(this);

        String accessToken = getIntent().getStringExtra("access_token");
        if (accessToken != null && !accessToken.isEmpty()) {
            Log.d(logTag, "onCreate: Access token received: " + accessToken);
            fetchActivityData(accessToken);
        } else {
            Log.e(logTag, "onCreate: Access token is empty or null");
            Toast.makeText(ActivitiesActivity.this, "Erreur: Jeton d'accès non disponible", Toast.LENGTH_SHORT).show();
        }
    }



    private void fetchActivityData(String accessToken) {
        Log.d(logTag, "fetchActivityData: Fetching activity data...");

        String url = getResources().getString(R.string.server_url_activity);

        JsonArrayRequest jsonArrayRequest = new JsonArrayRequest(Request.Method.GET, url, null,
                response -> {
                    Log.d(logTag, "fetchActivityData: Activity data fetched successfully");
                    List<String> activityList = parseActivityResponse(response);
                    displayActivity(activityList);
                },
                error -> {
                    String errorMessage = "Error: " + error.getMessage();
                    Log.e(logTag, "fetchActivityData: Error fetching activity data: " + errorMessage);
                    Toast.makeText(ActivitiesActivity.this, errorMessage, Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            public Map<String, String> getHeaders() {
                Map<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + accessToken);
                Log.d(logTag, Objects.requireNonNull(headers.put("Authorization", "Bearer " + accessToken)));
                return headers;
            }
        };

        queue.add(jsonArrayRequest);
    }


    private List<String> parseActivityResponse(JSONArray response) {
        List<String> activityList = new ArrayList<>();
        try {
            for (int i = 0; i < response.length(); i++) {
                JSONObject event = response.getJSONObject(i);
                String eventName = event.getString("eventName");
                String eventType = event.getString("eventType");
                String eventStart = event.getString("eventStart");
                String eventEnd = event.getString("eventEnd");
                String location = event.getString("location");
                String description = event.getString("description");
                String eventDetails = "Nom: " + eventName + "\n" +
                        "Type: " + eventType + "\n" +
                        "Début: " + eventStart + "\n" +
                        "Fin: " + eventEnd + "\n" +
                        "Lieu: " + location + "\n" +
                        "Description: " + description;
                activityList.add(eventDetails);
            }
        } catch (JSONException e) {
            Log.e(logTag, "Erreur de la récuperation du JSON:" + Arrays.toString(e.getStackTrace()));
        }
        return activityList;
    }


    private void displayActivity(List<String> activityList) {
        StringBuilder stringBuilder = new StringBuilder();
        for (String event : activityList) {
            stringBuilder.append(event).append("\n");
        }
        activityTextView.setText(stringBuilder.toString());
    }

}

