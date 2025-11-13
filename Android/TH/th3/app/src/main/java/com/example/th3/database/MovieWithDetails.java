package com.example.th3.database;

import androidx.room.Embedded;
import androidx.room.Relation;

public class MovieWithDetails {
    @Embedded
    public Movie movie;

    @Relation(
            parentColumn = "genreId",
            entityColumn = "id"
    )
    public Genre genre;

    @Relation(
            parentColumn = "cinemaId",
            entityColumn = "id"
    )
    public Cinema cinema;
}
