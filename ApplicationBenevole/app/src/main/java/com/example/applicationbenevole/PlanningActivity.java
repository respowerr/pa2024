package com.example.applicationbenevole;

import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
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

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.Objects;

public class PlanningActivity extends AppCompatActivity {


    private final String logTag = "APP_BENEVOLE";
    private TextView planningTextView;
    private RequestQueue queue;
    private String formattedDate;
    private Button previousDayButton;
    private Button nextDayButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_planning);

        Log.d(logTag, "onCreate: Planning created");

        planningTextView = findViewById(R.id.planningTextView);
        previousDayButton = findViewById(R.id.previousDayButton);
        nextDayButton = findViewById(R.id.nextDayButton);
        queue = Volley.newRequestQueue(this);

        String accessToken = getIntent().getStringExtra("access_token");
        if (accessToken != null && !accessToken.isEmpty()) {
            Log.d(logTag, "onCreate: Access token received: " + accessToken);
            fetchPlanningData(accessToken, formattedDate);
        } else {
            Log.e(logTag, "onCreate: Access token is empty or null");
            Toast.makeText(PlanningActivity.this, "Erreur: Jeton d'accès non disponible", Toast.LENGTH_SHORT).show();
        }

        previousDayButton.setOnClickListener(v -> {
            Calendar calendar = Calendar.getInstance();
            calendar.add(Calendar.DAY_OF_YEAR, -1);
            Date previousDate = calendar.getTime();

            SimpleDateFormat dateFormat = new SimpleDateFormat("dd-MM-yyyy", Locale.getDefault());
            formattedDate = dateFormat.format(previousDate);

            fetchPlanningData(accessToken, formattedDate);
        });

        nextDayButton.setOnClickListener(v -> {
            Calendar calendar = Calendar.getInstance();
            calendar.add(Calendar.DAY_OF_YEAR, 1);
            Date nextDate = calendar.getTime();

            SimpleDateFormat dateFormat = new SimpleDateFormat("dd-MM-yyyy", Locale.getDefault());
            formattedDate = dateFormat.format(nextDate);

            fetchPlanningData(accessToken, formattedDate);
        });


    }



    private void fetchPlanningData(String accessToken, String formattedDate) {
        Log.d(logTag, "fetchPlanningData: Fetching planning data...");

        String url = getResources().getString(R.string.server_url_activity) + "?date=" + formattedDate;

        JsonArrayRequest jsonArrayRequest = new JsonArrayRequest(Request.Method.GET, url, null,
                response -> {
                    Log.d(logTag, "fetchPlanningData: Planning data fetched successfully");
                    List<String> planningList = parsePlanningResponse(response);
                    displayPlanning(planningList);
                },
                error -> {
                    String errorMessage = "Error: " + error.getMessage();
                    Log.e(logTag, "fetchPlanningData: Error fetching planning data: " + errorMessage);
                    Toast.makeText(PlanningActivity.this, errorMessage, Toast.LENGTH_SHORT).show();
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



    private List<String> parsePlanningResponse(JSONArray response) {
        List<String> planningList = new ArrayList<>();
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
                planningList.add(eventDetails);
            }
        } catch (JSONException e) {
            Log.e(logTag, "Erreur de la récuperation du JSON:" + Arrays.toString(e.getStackTrace()));
        }
        return planningList;
    }


    private void displayPlanning(List<String> planningList) {
        StringBuilder stringBuilder = new StringBuilder();
        for (String event : planningList) {
            stringBuilder.append(event).append("\n");
        }
        planningTextView.setText(stringBuilder.toString());
    }

}
