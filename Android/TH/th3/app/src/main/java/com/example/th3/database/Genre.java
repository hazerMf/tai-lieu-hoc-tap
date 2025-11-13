package com.example.th3.database;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

@Entity(tableName = "genres")
public class Genre {
    @PrimaryKey(autoGenerate = true)
    private int id;
    private String name;
    private String categories; // Stored as comma-separated: "Hành động,Tình cảm,Hài hước"

    public Genre(String name, String categories) {
        this.name = name;
        this.categories = categories;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getCategories() {
        return categories;
    }

    public void setCategories(String categories) {
        this.categories = categories;
    }
}
