package com.example.th3.database;

import androidx.room.Entity;
import androidx.room.ForeignKey;
import androidx.room.PrimaryKey;

@Entity(tableName = "movies",
        foreignKeys = {
            @ForeignKey(entity = Genre.class,
                    parentColumns = "id",
                    childColumns = "genreId",
                    onDelete = ForeignKey.CASCADE),
            @ForeignKey(entity = Cinema.class,
                    parentColumns = "id",
                    childColumns = "cinemaId",
                    onDelete = ForeignKey.CASCADE)
        })
public class Movie {
    @PrimaryKey(autoGenerate = true)
    private int id;
    private int genreId;
    private int cinemaId;
    private String name;
    private long releaseDate; // Store as timestamp
    private double ticketPrice;

    public Movie(int genreId, int cinemaId, String name, long releaseDate, double ticketPrice) {
        this.genreId = genreId;
        this.cinemaId = cinemaId;
        this.name = name;
        this.releaseDate = releaseDate;
        this.ticketPrice = ticketPrice;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getGenreId() {
        return genreId;
    }

    public void setGenreId(int genreId) {
        this.genreId = genreId;
    }

    public int getCinemaId() {
        return cinemaId;
    }

    public void setCinemaId(int cinemaId) {
        this.cinemaId = cinemaId;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public long getReleaseDate() {
        return releaseDate;
    }

    public void setReleaseDate(long releaseDate) {
        this.releaseDate = releaseDate;
    }

    public double getTicketPrice() {
        return ticketPrice;
    }

    public void setTicketPrice(double ticketPrice) {
        this.ticketPrice = ticketPrice;
    }
}
