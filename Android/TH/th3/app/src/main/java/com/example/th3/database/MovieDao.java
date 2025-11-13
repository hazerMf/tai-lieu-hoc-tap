package com.example.th3.database;

import androidx.room.Dao;
import androidx.room.Delete;
import androidx.room.Insert;
import androidx.room.Query;
import androidx.room.Transaction;
import androidx.room.Update;

import java.util.List;

@Dao
public interface MovieDao {
    @Insert
    long insert(Movie movie);

    @Update
    void update(Movie movie);

    @Delete
    void delete(Movie movie);

    @Transaction
    @Query("SELECT * FROM movies ORDER BY releaseDate DESC, ticketPrice DESC")
    List<MovieWithDetails> getAllMoviesWithDetails();

    @Query("SELECT * FROM movies WHERE id = :id")
    Movie getMovieById(int id);

    @Transaction
    @Query("SELECT * FROM movies WHERE releaseDate >= :startDate AND releaseDate <= :endDate ORDER BY releaseDate DESC, ticketPrice DESC")
    List<MovieWithDetails> getMoviesByDateRange(long startDate, long endDate);

    @Query("SELECT COUNT(*) FROM movies m INNER JOIN genres g ON m.genreId = g.id WHERE g.categories LIKE '%' || :category || '%'")
    int countMoviesByCategory(String category);
}
