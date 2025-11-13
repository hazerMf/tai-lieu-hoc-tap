package com.example.th3.database;

import androidx.room.Dao;
import androidx.room.Delete;
import androidx.room.Insert;
import androidx.room.Query;
import androidx.room.Update;

import java.util.List;

@Dao
public interface GenreDao {
    @Insert
    long insert(Genre genre);

    @Update
    void update(Genre genre);

    @Delete
    void delete(Genre genre);

    @Query("SELECT * FROM genres ORDER BY name ASC")
    List<Genre> getAllGenres();

    @Query("SELECT * FROM genres WHERE id = :id")
    Genre getGenreById(int id);

    @Query("SELECT * FROM genres WHERE name LIKE '%' || :searchText || '%'")
    List<Genre> searchByName(String searchText);

    @Query("SELECT * FROM genres WHERE categories LIKE '%' || :category || '%'")
    List<Genre> filterByCategory(String category);
}
