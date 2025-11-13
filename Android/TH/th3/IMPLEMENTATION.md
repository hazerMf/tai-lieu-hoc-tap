# Chi tiết triển khai - Ứng dụng Quản lý Phim

## 1. Database Schema

### Bảng: genres (Thể loại phim)
```sql
CREATE TABLE genres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    categories TEXT NOT NULL  -- Lưu dạng: "Hành động,Tình cảm,Hài hước"
)
```

### Bảng: cinemas (Rạp chiếu)
```sql
CREATE TABLE cinemas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    address TEXT NOT NULL
)
```

### Bảng: movies (Phim)
```sql
CREATE TABLE movies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    genreId INTEGER NOT NULL,
    cinemaId INTEGER NOT NULL,
    name TEXT NOT NULL,
    releaseDate INTEGER NOT NULL,  -- Timestamp (milliseconds)
    ticketPrice REAL NOT NULL,
    FOREIGN KEY (genreId) REFERENCES genres(id) ON DELETE CASCADE,
    FOREIGN KEY (cinemaId) REFERENCES cinemas(id) ON DELETE CASCADE
)
```

## 2. Các tính năng chính

### A. Fragment Thể loại phim (GenreListFragment)

**Tìm kiếm theo tên:**
```java
searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
    @Override
    public boolean onQueryTextChange(String newText) {
        filterGenres();
        return true;
    }
});
```

**Lọc theo danh mục (checkbox):**
```java
private void filterGenres() {
    List<String> selectedCategories = new ArrayList<>();
    if (cbAction.isChecked()) selectedCategories.add("Hành động");
    if (cbRomance.isChecked()) selectedCategories.add("Tình cảm");
    if (cbComedy.isChecked()) selectedCategories.add("Hài hước");
    
    // Filter logic
    for (Genre genre : allGenres) {
        boolean matchesCategory = selectedCategories.isEmpty();
        if (!matchesCategory) {
            for (String category : selectedCategories) {
                if (genre.getCategories().contains(category)) {
                    matchesCategory = true;
                    break;
                }
            }
        }
        // Add to filtered list if matches
    }
}
```

### B. Fragment Danh sách phim (MovieListFragment)

**Lọc theo khoảng năm:**
```java
btnFilter.setOnClickListener(v -> {
    int fromYear = Integer.parseInt(etFromYear.getText().toString());
    int toYear = Integer.parseInt(etToYear.getText().toString());
    
    Calendar calStart = Calendar.getInstance();
    calStart.set(fromYear, Calendar.JANUARY, 1, 0, 0, 0);
    
    Calendar calEnd = Calendar.getInstance();
    calEnd.set(toYear, Calendar.DECEMBER, 31, 23, 59, 59);
    
    List<MovieWithDetails> filteredMovies = database.movieDao()
        .getMoviesByDateRange(calStart.getTimeInMillis(), calEnd.getTimeInMillis());
    adapter.setMovies(filteredMovies);
});
```

**Sắp xếp (trong MovieDao):**
```java
@Query("SELECT * FROM movies ORDER BY releaseDate DESC, ticketPrice DESC")
List<MovieWithDetails> getAllMoviesWithDetails();
```

**Thống kê theo danh mục:**
```java
int actionCount = database.movieDao().countMoviesByCategory("Hành động");
int romanceCount = database.movieDao().countMoviesByCategory("Tình cảm");
int comedyCount = database.movieDao().countMoviesByCategory("Hài hước");

String stats = String.format("Thống kê:\nHành động: %d | Tình cảm: %d | Hài hước: %d",
        actionCount, romanceCount, comedyCount);
```

**Query thống kê (trong MovieDao):**
```java
@Query("SELECT COUNT(*) FROM movies m INNER JOIN genres g ON m.genreId = g.id 
        WHERE g.categories LIKE '%' || :category || '%'")
int countMoviesByCategory(String category);
```

### C. Thêm/Sửa phim (AddEditMovieActivity)

**DatePickerDialog:**
```java
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
```

**Validation:**
```java
private void saveMovie() {
    String movieName = etMovieName.getText().toString().trim();
    String priceStr = etTicketPrice.getText().toString().trim();
    
    if (movieName.isEmpty()) {
        etMovieName.setError("Vui lòng nhập tên phim");
        return;
    }
    
    try {
        double ticketPrice = Double.parseDouble(priceStr);
        if (ticketPrice <= 0) {
            etTicketPrice.setError("Giá vé phải lớn hơn 0");
            return;
        }
    } catch (NumberFormatException e) {
        etTicketPrice.setError("Giá vé không hợp lệ");
        return;
    }
    
    // Save to database
}
```

**Spinner setup:**
```java
private void loadSpinnerData() {
    genres = database.genreDao().getAllGenres();
    String[] genreNames = new String[genres.size()];
    for (int i = 0; i < genres.size(); i++) {
        genreNames[i] = genres.get(i).getName();
    }
    ArrayAdapter<String> genreAdapter = new ArrayAdapter<>(this,
            android.R.layout.simple_spinner_item, genreNames);
    spinnerGenre.setAdapter(genreAdapter);
}
```

### D. Xóa phim với confirmation dialog

```java
holder.btnDelete.setOnClickListener(v -> {
    new AlertDialog.Builder(context)
        .setTitle("Xác nhận xóa")
        .setMessage("Bạn có chắc muốn xóa phim \"" + movieWithDetails.movie.getName() + "\"?")
        .setPositiveButton("Xóa", (dialog, which) -> {
            database.movieDao().delete(movieWithDetails.movie);
            loadMovies();
        })
        .setNegativeButton("Hủy", null)
        .show();
});
```

## 3. Dependencies

```kotlin
// Room Database
implementation("androidx.room:room-runtime:2.6.1")
annotationProcessor("androidx.room:room-compiler:2.6.1")

// ViewPager2
implementation("androidx.viewpager2:viewpager2:1.1.0")

// Material Design
implementation("com.google.android.material:material:1.13.0")
```

## 4. Key Features Summary

| Tính năng | Triển khai | Status |
|-----------|-----------|--------|
| 3 bảng database | Room with @Entity, @Dao | ✅ |
| Foreign Keys | @ForeignKey annotations | ✅ |
| TabLayout | MainActivity + ViewPager2 | ✅ |
| 2 Fragments | GenreList + MovieList | ✅ |
| FAB | FloatingActionButton | ✅ |
| Search | SearchView + filter logic | ✅ |
| Filter by category | CheckBox + LIKE query | ✅ |
| Filter by year | Date range query | ✅ |
| Sort | ORDER BY releaseDate DESC, price DESC | ✅ |
| Statistics | COUNT query with JOIN | ✅ |
| Add movie | Spinner + DatePicker + Validation | ✅ |
| Edit movie | Load data + Update | ✅ |
| Delete movie | AlertDialog + DAO.delete() | ✅ |
| Sample data | Database.Callback onCreate() | ✅ |

## 5. Testing Guide

### Test Case 1: Thêm phim mới
1. Click FAB
2. Chọn thể loại từ spinner
3. Chọn rạp từ spinner
4. Nhập tên phim
5. Click chọn ngày
6. Nhập giá vé
7. Click Lưu
8. Kiểm tra phim xuất hiện trong tab "Danh sách phim"

### Test Case 2: Sửa phim
1. Click vào phim trong danh sách
2. Sửa thông tin
3. Click Lưu
4. Kiểm tra thông tin đã được cập nhật

### Test Case 3: Xóa phim
1. Click nút xóa (biểu tượng thùng rác)
2. Xác nhận trong dialog
3. Kiểm tra phim đã bị xóa khỏi danh sách

### Test Case 4: Tìm kiếm thể loại
1. Chuyển sang tab "Thể loại phim"
2. Nhập từ khóa vào SearchView
3. Kiểm tra kết quả lọc

### Test Case 5: Lọc theo danh mục
1. Check "Hành động"
2. Kiểm tra chỉ hiện thể loại có danh mục Hành động
3. Check thêm "Tình cảm"
4. Kiểm tra hiện cả 2 danh mục

### Test Case 6: Lọc theo năm
1. Chuyển sang tab "Danh sách phim"
2. Nhập từ năm: 2024
3. Nhập đến năm: 2025
4. Click "Lọc"
5. Kiểm tra chỉ hiện phim từ 2024-2025

### Test Case 7: Kiểm tra sắp xếp
1. Xem danh sách phim
2. Kiểm tra phim mới nhất ở trên cùng
3. Với cùng ngày, phim có giá cao hơn ở trên

### Test Case 8: Thống kê
1. Xem "Thống kê" ở đầu tab Danh sách phim
2. Kiểm tra số lượng phim từng danh mục
3. Thêm phim mới và kiểm tra số liệu cập nhật

## 6. Troubleshooting

**Vấn đề: Database không có dữ liệu mẫu**
- Giải pháp: Xóa app và cài lại để trigger onCreate callback

**Vấn đề: Build error với Room**
- Giải pháp: Rebuild project (Build > Rebuild Project)

**Vấn đề: Tabs không chuyển được**
- Giải pháp: Kiểm tra ViewPager2 adapter và TabLayoutMediator

**Vấn đề: SearchView không hoạt động**
- Giải pháp: Kiểm tra OnQueryTextListener implementation
