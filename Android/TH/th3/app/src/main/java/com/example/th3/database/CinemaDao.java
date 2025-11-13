package com.example.th3.database;

import androidx.room.Dao;
import androidx.room.Delete;
import androidx.room.Insert;
import androidx.room.Query;
import androidx.room.Update;

import java.util.List;

@Dao
public interface CinemaDao {
    @Insert
    long insert(Cinema cinema);

    @Update
    void update(Cinema cinema);

    @Delete
    void delete(Cinema cinema);

    @Query("SELECT * FROM cinemas ORDER BY name ASC")
    List<Cinema> getAllCinemas();

    @Query("SELECT * FROM cinemas WHERE id = :id")
    Cinema getCinemaById(int id);
}
