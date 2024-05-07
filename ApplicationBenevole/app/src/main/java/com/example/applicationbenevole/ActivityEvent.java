package com.example.applicationbenevole;

public class ActivityEvent {
    private String eventName;
    private String eventType;
    private String eventStart;
    private String eventEnd;
    private String location;
    private String description;

    public ActivityEvent(String eventName, String eventType, String eventStart, String eventEnd, String location, String description) {
        this.eventName = eventName;
        this.eventType = eventType;
        this.eventStart = eventStart;
        this.eventEnd = eventEnd;
        this.location = location;
        this.description = description;
    }

    public String getEventName() {
        return eventName;
    }

    public String getEventType() {
        return eventType;
    }

    public String getEventStart() {
        return eventStart;
    }

    public String getEventEnd() {
        return eventEnd;
    }

    public String getLocation() {
        return location;
    }

    public String getDescription() {
        return description;
    }
}
