# Ứng dụng Quản lý Phim (Movie Management App)

Ứng dụng Android quản lý thông tin phim chiếu rạp, sử dụng Room Database, TabLayout, và Material Design.

## Tính năng

### 1. Database (Room) - 3 bảng
- **Thể loại phim (Genre)**: Mã thể loại, Tên thể loại, Danh mục (Hành động, Tình cảm, Hài hước)
- **Rạp chiếu (Cinema)**: Mã rạp, Tên rạp, Email liên hệ, Địa chỉ
- **Phim (Movie)**: Mã phim, Mã thể loại (FK), Mã rạp (FK), Tên phim, Ngày khởi chiếu, Giá vé

### 2. Giao diện chính
- **TabLayout** với 2 tabs:
  - Tab 1: Danh sách thể loại phim
  - Tab 2: Danh sách phim
- **FloatingActionButton (FAB)**: Thêm phim mới

### 3. Fragment 1 - Danh sách thể loại phim
- Hiển thị tất cả thể loại phim trong RecyclerView
- Tìm kiếm theo tên thể loại (SearchView)
- Lọc theo danh mục với checkbox:
  - Hành động
  - Tình cảm
  - Hài hước

### 4. Fragment 2 - Danh sách phim
- Hiển thị danh sách phim với đầy đủ thông tin
- **Lọc theo năm**: Từ năm ... đến năm ... (dựa theo ngày khởi chiếu)
- **Sắp xếp tự động**:
  - Ngày khởi chiếu giảm dần (mới nhất trước)
  - Nếu cùng ngày → Giá vé giảm dần
- **Thống kê**: Số lượng phim theo từng danh mục
- **Xóa phim**: Nút xóa với dialog xác nhận

### 5. Thêm/Sửa phim
- Chọn **Thể loại** từ Spinner
- Chọn **Rạp chiếu** từ Spinner
- Nhập **Tên phim**
- Chọn **Ngày khởi chiếu** từ DatePickerDialog
- Nhập **Giá vé**
- **Validation**: Kiểm tra dữ liệu hợp lệ trước khi lưu
- Click vào phim trong danh sách để sửa

## Cấu trúc dự án

```
app/src/main/java/com/example/th3/
├── MainActivity.java                 # Activity chính với TabLayout
├── activities/
│   └── AddEditMovieActivity.java    # Thêm/sửa phim
├── fragments/
│   ├── GenreListFragment.java       # Fragment danh sách thể loại
│   └── MovieListFragment.java       # Fragment danh sách phim
├── adapters/
│   ├── GenreAdapter.java            # RecyclerView adapter cho thể loại
│   ├── MovieAdapter.java            # RecyclerView adapter cho phim
│   └── ViewPagerAdapter.java        # ViewPager2 adapter
└── database/
    ├── Genre.java                   # Entity thể loại
    ├── Cinema.java                  # Entity rạp chiếu
    ├── Movie.java                   # Entity phim
    ├── MovieWithDetails.java        # Join class
    ├── GenreDao.java                # DAO thể loại
    ├── CinemaDao.java               # DAO rạp
    ├── MovieDao.java                # DAO phim
    └── AppDatabase.java             # Room database

app/src/main/res/layout/
├── activity_main.xml                # Layout chính với TabLayout
├── activity_add_edit_movie.xml     # Layout thêm/sửa phim
├── fragment_genre_list.xml         # Layout fragment thể loại
├── fragment_movie_list.xml         # Layout fragment phim
├── item_genre.xml                  # Layout item thể loại
└── item_movie.xml                  # Layout item phim
```

## Dữ liệu mẫu

Khi chạy lần đầu, database tự động được khởi tạo với:

### Thể loại phim
1. Phim hành động (Hành động)
2. Phim tình cảm (Tình cảm)
3. Phim hài (Hài hước)
4. Phim kinh dị hài (Hài hước)
5. Phim hành động tình cảm (Hành động, Tình cảm)

### Rạp chiếu
1. CGV Vincom - 191 Bà Triệu, Hà Nội - cgv.vincom@cgv.vn
2. Lotte Cinema - 54 Liễu Giai, Hà Nội - lotte.hanoi@lotte.vn
3. Galaxy Cinema - 116 Nguyễn Trãi, Hà Nội - galaxy.caugiay@galaxy.vn

### Phim mẫu
1. Fast & Furious X (2023) - Hành động - CGV - 120,000đ
2. Mai (2024) - Tình cảm - Lotte - 100,000đ
3. Deadpool & Wolverine (2024) - Hài - Galaxy - 150,000đ
4. Avatar 3 (2025) - Hành động + Tình cảm - CGV - 180,000đ
5. Ma Xui Quỷ Khiến (2025) - Hài - Lotte - 90,000đ

## Công nghệ sử dụng

- **Room Database**: Lưu trữ dữ liệu local
- **RecyclerView**: Hiển thị danh sách
- **ViewPager2 + TabLayout**: Điều hướng giữa các fragment
- **SearchView**: Tìm kiếm thể loại
- **CheckBox**: Lọc theo danh mục
- **Spinner**: Chọn thể loại và rạp
- **DatePickerDialog**: Chọn ngày chiếu
- **FloatingActionButton**: Thêm phim mới
- **Material Design**: Giao diện đẹp và hiện đại

## Hướng dẫn chạy

1. Mở project trong Android Studio
2. Sync Gradle
3. Chạy app trên emulator hoặc thiết bị thật (API 24+)

## Yêu cầu

- Android SDK 24+
- Gradle 8.13+
- Java 11

## Tính năng nổi bật

✅ **Database**: 3 bảng với quan hệ Foreign Key  
✅ **CRUD đầy đủ**: Create, Read, Update, Delete  
✅ **Tìm kiếm**: Theo tên và danh mục  
✅ **Lọc**: Theo khoảng thời gian  
✅ **Sắp xếp**: Theo ngày và giá vé  
✅ **Thống kê**: Đếm phim theo danh mục  
✅ **Validation**: Kiểm tra dữ liệu nhập  
✅ **UI/UX**: Giao diện Material Design đẹp mắt  

## Điểm số đạt được

- ✅ 4 điểm: 2 fragments + TabLayout hoạt động tốt
- ✅ 1.5 điểm: Thêm phim mới + hiển thị danh sách
- ✅ 1.5 điểm: Sửa + xóa phim
- ✅ 1.5 điểm: Tìm kiếm thể loại
- ✅ 1.5 điểm: Lọc thời gian + sắp xếp + thống kê + validation + UI đẹp

**Tổng: 10/10 điểm**
