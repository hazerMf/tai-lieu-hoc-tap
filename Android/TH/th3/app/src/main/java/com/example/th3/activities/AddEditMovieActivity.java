package com.example.th3.activities;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.th3.R;
import com.example.th3.database.AppDatabase;
import com.example.th3.database.Cinema;
import com.example.th3.database.Genre;
import com.example.th3.database.Movie;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;

public class AddEditMovieActivity extends AppCompatActivity {
    private Spinner spinnerGenre, spinnerCinema;
    private EditText etMovieName, etTicketPrice, etReleaseDate;
    private Button btnSave, btnCancel;
    private AppDatabase database;
    private Calendar selectedDate;
    private int movieId = -1;
    private Movie currentMovie;

    private List<Genre> genres;
    private List<Cinema> cinemas;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_add_edit_movie);

        database = AppDatabase.getDatabase(this);
        selectedDate = Calendar.getInstance();

        initViews();
        loadSpinnerData();
        
        // Check if editing existing movie
        movieId = getIntent().getIntExtra("movieId", -1);
        if (movieId != -1) {
            setTitle("Sửa phim");
            loadMovieData();
        } else {
            setTitle("Thêm phim mới");
        }

        setupDatePicker();
        setupButtons();
    }

    private void initViews() {
        spinnerGenre = findViewById(R.id.spinnerGenre);
        spinnerCinema = findViewById(R.id.spinnerCinema);
        etMovieName = findViewById(R.id.etMovieName);
        etTicketPrice = findViewById(R.id.etTicketPrice);
        etReleaseDate = findViewById(R.id.etReleaseDate);
        btnSave = findViewById(R.id.btnSave);
        btnCancel = findViewById(R.id.btnCancel);
    }

    private void loadSpinnerData() {
        genres = database.genreDao().getAllGenres();
        String[] genreNames = new String[genres.size()];
        for (int i = 0; i < genres.size(); i++) {
            genreNames[i] = genres.get(i).getName();
        }
        ArrayAdapter<String> genreAdapter = new ArrayAdapter<>(this,
                android.R.layout.simple_spinner_item, genreNames);
        genreAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerGenre.setAdapter(genreAdapter);

        cinemas = database.cinemaDao().getAllCinemas();
        String[] cinemaNames = new String[cinemas.size()];
        for (int i = 0; i < cinemas.size(); i++) {
            cinemaNames[i] = cinemas.get(i).getName();
        }
        ArrayAdapter<String> cinemaAdapter = new ArrayAdapter<>(this,
                android.R.layout.simple_spinner_item, cinemaNames);
        cinemaAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerCinema.setAdapter(cinemaAdapter);
    }

    private void loadMovieData() {
        currentMovie = database.movieDao().getMovieById(movieId);
        if (currentMovie != null) {
            etMovieName.setText(currentMovie.getName());
            etTicketPrice.setText(String.valueOf(currentMovie.getTicketPrice()));
            
            selectedDate.setTimeInMillis(currentMovie.getReleaseDate());
            updateDateDisplay();

            // Set spinner selections
            for (int i = 0; i < genres.size(); i++) {
                if (genres.get(i).getId() == currentMovie.getGenreId()) {
                    spinnerGenre.setSelection(i);
                    break;
                }
            }
            
            for (int i = 0; i < cinemas.size(); i++) {
                if (cinemas.get(i).getId() == currentMovie.getCinemaId()) {
                    spinnerCinema.setSelection(i);
                    break;
                }
            }
        }
    }

    private void setupDatePicker() {
        etReleaseDate.setFocusable(false);
        etReleaseDate.setOnClickListener(v -> {
            DatePickerDialog datePickerDialog = new DatePickerDialog(
                    AddEditMovieActivity.this,
                    (view, year, month, dayOfMonth) -> {
                        selectedDate.set(year, month, dayOfMonth);
                        updateDateDisplay();
                    },
                    selectedDate.get(Calendar.YEAR),
                    selectedDate.get(Calendar.MONTH),
                    selectedDate.get(Calendar.DAY_OF_MONTH)
            );
            datePickerDialog.show();
        });
        
        updateDateDisplay();
    }

    private void updateDateDisplay() {
        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
        etReleaseDate.setText(sdf.format(selectedDate.getTime()));
    }

    private void setupButtons() {
        btnSave.setOnClickListener(v -> saveMovie());
        btnCancel.setOnClickListener(v -> finish());
    }

    private void saveMovie() {
        String movieName = etMovieName.getText().toString().trim();
        String priceStr = etTicketPrice.getText().toString().trim();

        // Validation
        if (movieName.isEmpty()) {
            etMovieName.setError("Vui lòng nhập tên phim");
            etMovieName.requestFocus();
            return;
        }

        if (priceStr.isEmpty()) {
            etTicketPrice.setError("Vui lòng nhập giá vé");
            etTicketPrice.requestFocus();
            return;
        }

        double ticketPrice;
        try {
            ticketPrice = Double.parseDouble(priceStr);
            if (ticketPrice <= 0) {
                etTicketPrice.setError("Giá vé phải lớn hơn 0");
                etTicketPrice.requestFocus();
                return;
            }
        } catch (NumberFormatException e) {
            etTicketPrice.setError("Giá vé không hợp lệ");
            etTicketPrice.requestFocus();
            return;
        }

        int selectedGenreId = genres.get(spinnerGenre.getSelectedItemPosition()).getId();
        int selectedCinemaId = cinemas.get(spinnerCinema.getSelectedItemPosition()).getId();

        if (movieId == -1) {
            // Add new movie
            Movie movie = new Movie(selectedGenreId, selectedCinemaId, movieName,
                    selectedDate.getTimeInMillis(), ticketPrice);
            database.movieDao().insert(movie);
            Toast.makeText(this, "Đã thêm phim thành công", Toast.LENGTH_SHORT).show();
        } else {
            // Update existing movie
            currentMovie.setName(movieName);
            currentMovie.setGenreId(selectedGenreId);
            currentMovie.setCinemaId(selectedCinemaId);
            currentMovie.setReleaseDate(selectedDate.getTimeInMillis());
            currentMovie.setTicketPrice(ticketPrice);
            database.movieDao().update(currentMovie);
            Toast.makeText(this, "Đã cập nhật phim thành công", Toast.LENGTH_SHORT).show();
        }

        finish();
    }
}
