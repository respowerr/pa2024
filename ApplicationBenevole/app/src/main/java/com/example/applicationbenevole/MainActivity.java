package com.example.applicationbenevole;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {

    private final String logTag = "APP_BENEVOLE";
    private SharedPreferences sharedPreferences;
    private Button loginButton;
    private boolean isLoggedIn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        sharedPreferences = getSharedPreferences("user_session", MODE_PRIVATE);
        isLoggedIn = sharedPreferences.getBoolean("is_logged_in", false);

        TextView welcomeTextView = findViewById(R.id.welcomeTextView);
        loginButton = findViewById(R.id.loginButton);
        Button planningButton = findViewById(R.id.planningButton);
        Button profileButton = findViewById(R.id.profileButton);
        Button activitiesButton = findViewById(R.id.activitiesButton);

        updateLoginButton(isLoggedIn);

        loginButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (isLoggedIn) {
                    logoutUser();
                } else {
                    startActivity(new Intent(MainActivity.this, LoginActivity.class));
                }

            }

        });

        String accessToken = getIntent().getStringExtra("access_token");
        if (accessToken != null) {
            Log.d(logTag, accessToken);
        } else {
            Log.d(logTag, "Access token non trouvé dans l'intent");
        }

        activitiesButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String accessToken = getIntent().getStringExtra("access_token");
                if (accessToken != null) {
                    Intent intent = new Intent(MainActivity.this, ActivitiesActivity.class);
                    intent.putExtra("access_token", accessToken);
                    Log.d(logTag, accessToken);
                    startActivity(intent);
                } else {

                    Toast.makeText(MainActivity.this, "Vous devez vous connecter pour accéder au planning.", Toast.LENGTH_SHORT).show();
                }
            }
        });


        profileButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String accessToken = getIntent().getStringExtra("access_token");
                int userId = getIntent().getIntExtra("user_id", 0);
                if (accessToken != null && userId != 0) {
                    Intent intent = new Intent(MainActivity.this, ProfileActivity.class);
                    intent.putExtra("access_token", accessToken);
                    intent.putExtra("user_id", userId);
                    Log.w(logTag, String.valueOf(userId));
                    startActivity(intent);
                } else {
                    Toast.makeText(MainActivity.this, "Vous devez vous connecter pour accéder au profil.", Toast.LENGTH_SHORT).show();
                }
            }
        });



        planningButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String accessToken = getIntent().getStringExtra("access_token");
                int userId = getIntent().getIntExtra("user_id", 0);
                if (accessToken != null && userId != 0) {
                    Intent intent = new Intent(MainActivity.this, PlanningActivity.class);
                    intent.putExtra("access_token", accessToken);
                    intent.putExtra("user_id", userId);
                    Log.w(logTag, String.valueOf(userId));
                    startActivity(intent);
                } else {
                    Toast.makeText(MainActivity.this, "Vous devez vous connecter pour accéder au profil.", Toast.LENGTH_SHORT).show();
                }
            }
        });
    }


    private void updateLoginButton(boolean isLoggedIn) {
        if (isLoggedIn) {
            loginButton.setText("Se déconnecter");
        } else {
            loginButton.setText("Se connecter");
        }
    }

    private void logoutUser() {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.remove("is_logged_in");
        editor.remove("access_token");
        editor.apply();
        boolean isLoggedIn = false;
        updateLoginButton(isLoggedIn);
    }

}
