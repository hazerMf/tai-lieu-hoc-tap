package com.example.th3.database;

import android.content.Context;

import androidx.room.Database;
import androidx.room.Room;
import androidx.room.RoomDatabase;

import java.util.Calendar;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

@Database(entities = {Genre.class, Cinema.class, Movie.class}, version = 1, exportSchema = false)
public abstract class AppDatabase extends RoomDatabase {
    public abstract GenreDao genreDao();
    public abstract CinemaDao cinemaDao();
    public abstract MovieDao movieDao();

    private static volatile AppDatabase INSTANCE;
    private static final int NUMBER_OF_THREADS = 4;
    public static final ExecutorService databaseWriteExecutor = Executors.newFixedThreadPool(NUMBER_OF_THREADS);

    public static AppDatabase getDatabase(final Context context) {
        if (INSTANCE == null) {
            synchronized (AppDatabase.class) {
                if (INSTANCE == null) {
                    INSTANCE = Room.databaseBuilder(context.getApplicationContext(),
                                    AppDatabase.class, "movie_database")
                            .allowMainThreadQueries() // For simplicity - not recommended in production
                            .build();
                    
                    // Check if database is empty, then populate sample data
                    if (INSTANCE.genreDao().getAllGenres().isEmpty()) {
                        populateSampleData(context);
                    }
                }
            }
        }
        return INSTANCE;
    }

    private static void populateSampleData(Context context) {
        AppDatabase db = getDatabase(context);
        GenreDao genreDao = db.genreDao();
        CinemaDao cinemaDao = db.cinemaDao();
        MovieDao movieDao = db.movieDao();

        // Add sample genres
        Genre genre1 = new Genre("Phim hành động", "Hành động");
        Genre genre2 = new Genre("Phim tình cảm", "Tình cảm");
        Genre genre3 = new Genre("Phim hài", "Hài hước");
        Genre genre4 = new Genre("Phim kinh dị", "Kinh dị");
        Genre genre5 = new Genre("Phim kinh dị hài", "Kinh dị,Hài hước");
        Genre genre6 = new Genre("Phim hành động tình cảm", "Hành động,Tình cảm");

        long genreId1 = genreDao.insert(genre1);
        long genreId2 = genreDao.insert(genre2);
        long genreId3 = genreDao.insert(genre3);
        long genreId4 = genreDao.insert(genre4);
        long genreId5 = genreDao.insert(genre5);
        long genreId6 = genreDao.insert(genre6);

        // Add sample cinemas
        Cinema cinema1 = new Cinema("CGV Vincom", "cgv.vincom@cgv.vn", "191 Bà Triệu, Hà Nội");
        Cinema cinema2 = new Cinema("Lotte Cinema", "lotte.hanoi@lotte.vn", "54 Liễu Giai, Hà Nội");
        Cinema cinema3 = new Cinema("Galaxy Cinema", "galaxy.caugiay@galaxy.vn", "116 Nguyễn Trãi, Hà Nội");

        long cinemaId1 = cinemaDao.insert(cinema1);
        long cinemaId2 = cinemaDao.insert(cinema2);
        long cinemaId3 = cinemaDao.insert(cinema3);

        // Add sample movies
        Calendar cal = Calendar.getInstance();
        
        // Movie 1: 2023
        cal.set(2023, Calendar.MARCH, 15);
        movieDao.insert(new Movie((int)genreId1, (int)cinemaId1, "Fast & Furious X", cal.getTimeInMillis(), 120000));

        // Movie 2: 2024
        cal.set(2024, Calendar.JANUARY, 20);
        movieDao.insert(new Movie((int)genreId2, (int)cinemaId2, "Mai", cal.getTimeInMillis(), 100000));

        // Movie 3: 2024
        cal.set(2024, Calendar.JULY, 10);
        movieDao.insert(new Movie((int)genreId3, (int)cinemaId3, "Deadpool & Wolverine", cal.getTimeInMillis(), 150000));

        // Movie 4: 2025
        cal.set(2025, Calendar.FEBRUARY, 1);
        movieDao.insert(new Movie((int)genreId6, (int)cinemaId1, "Avatar 3", cal.getTimeInMillis(), 180000));

        // Movie 5: 2025 (same date as Movie 4, but lower price)
        cal.set(2025, Calendar.FEBRUARY, 1);
        movieDao.insert(new Movie((int)genreId5, (int)cinemaId2, "Ma Xui Quỷ Khiến", cal.getTimeInMillis(), 90000));

        // Movie 6: 2024 Horror
        cal.set(2024, Calendar.OCTOBER, 31);
        movieDao.insert(new Movie((int)genreId4, (int)cinemaId3, "The Nun 2", cal.getTimeInMillis(), 110000));
    }
}
