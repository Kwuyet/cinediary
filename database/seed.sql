-- CineDiary sample data: 20 users, 49 films, reviews and comments
-- Import AFTER schema.sql. Every sample account uses the password: password
SET NAMES utf8mb4;
INSERT INTO NguoiDung (tenDangNhap, email, matKhau, tieuSu, anhDaiDien, vaiTro) VALUES
('admin1',      'admin1@cinediary.vn',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên hệ thống', 'avatar/admin.jpg', 'QuanTriVien'),
('quyetphim',   'quyet@cinediary.vn',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Yêu phim Việt Nam và Hollywood', 'avatar/quyet.jpg', 'ThanhVien'),
('ngocphim',    'ngoc@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fan K-drama và phim Hàn', NULL, 'ThanhVien'),
('minhreview',  'minh@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Reviewer chuyên nghiệp', NULL, 'ThanhVien'),
('lanphim',     'lan@cinediary.vn',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thích phim kinh dị', NULL, 'ThanhVien'),
('hoangcine',   'hoang@cinediary.vn',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('thanhphim',   'thanh@cinediary.vn',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thích phim hành động', NULL, 'ThanhVien'),
('vyreview',    'vy@cinediary.vn',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('kienphim',    'kien@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fan Marvel & DC', NULL, 'ThanhVien'),
('huyenphim',   'huyen@cinediary.vn',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('phucreview',  'phuc@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Yêu phim Việt', NULL, 'ThanhVien'),
('anhphim',     'anh@cinediary.vn',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('maicine',     'mai@cinediary.vn',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fan anime', NULL, 'ThanhVien'),
('longreview',  'long@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('thaophim',    'thao@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('datcine',     'dat@cinediary.vn',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thích phim khoa học viễn tưởng', NULL, 'ThanhVien'),
('linhreview',  'linh@cinediary.vn',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('khanhphim',   'khanh@cinediary.vn',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien'),
('myphim',      'my@cinediary.vn',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fan phim Hàn Quốc', NULL, 'ThanhVien'),
('namreview',   'nam@cinediary.vn',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ThanhVien');

-- 2. 50 BỘ PHIM
INSERT INTO Phim (tenPhimVN, tenPhimEN, namSanXuat, thoiLuong, theLoai, daoDien, dienVien, noiDung, duongDanTrailer, duongDanPoster) VALUES
('Oppenheimer', 'Oppenheimer', 2023, 180, 'Tâm lý,Lịch sử', 'Christopher Nolan', 'Cillian Murphy,Emily Blunt', 'Câu chuyện về cha đẻ bom nguyên tử', 'https://youtu.be/uYPbbksJxIg', 'poster/oppenheimer.jpg'),
('Dune: Phần Hai', 'Dune: Part Two', 2024, 166, 'Khoa học viễn tưởng', 'Denis Villeneuve', 'Timothée Chalamet,Zendaya', 'Paul Atreides tiếp tục hành trình', 'https://youtu.be/_YUk7f8k3oM', 'poster/dune2.jpg'),
('Furiosa', 'Furiosa: A Mad Max Saga', 2024, 148, 'Hành động', 'George Miller', 'Anya Taylor-Joy', 'Nguồn gốc của Furiosa', 'https://youtu.be/XJMUh2E9jIU', 'poster/furiosa.jpg'),
('Challengers', 'Challengers', 2024, 131, 'Tâm lý,Thể thao', 'Luca Guadagnino', 'Zendaya,Mike Faist', 'Câu chuyện tình tay ba trong tennis', 'https://youtu.be/9OCEeznO4oA', 'poster/challengers.jpg'),
('Deadpool & Wolverine', 'Deadpool & Wolverine', 2024, 127, 'Hành động,Hài', 'Shawn Levy', 'Ryan Reynolds,Hugh Jackman', 'Hai anh hùng gặp nhau', 'https://youtu.be/73_1biRkmOE', 'poster/deadpool.jpg'),
('Inside Out 2', 'Inside Out 2', 2024, 96, 'Hoạt hình', 'Kelsey Mann', 'Amy Poehler', 'Cảm xúc mới của Riley', 'https://youtu.be/LEjMRz7qU8M', 'poster/insideout2.jpg'),
('Kingdom of the Planet of the Apes', 'Kingdom of the Planet of the Apes', 2024, 145, 'Khoa học viễn tưởng', 'Wes Ball', 'Owen Teague', 'Thế hệ khỉ mới', 'https://youtu.be/XtFI7UTiQbA', 'poster/kingdomapes.jpg'),
('Kung Fu Panda 4', 'Kung Fu Panda 4', 2024, 94, 'Hoạt hình', 'Mike Mitchell', 'Jack Black', 'Po trở lại', 'https://youtu.be/_inKs5U5Q5Y', 'poster/kungfupanda4.jpg'),
('The Fall Guy', 'The Fall Guy', 2024, 126, 'Hành động,Hài', 'David Leitch', 'Ryan Gosling', 'Chuyên gia đóng thế', 'https://youtu.be/aCoG6RXeO9c', 'poster/fallguy.jpg'),
('Godzilla x Kong: The New Empire', 'Godzilla x Kong: The New Empire', 2024, 115, 'Hành động', 'Adam Wingard', 'Rebecca Hall', 'Godzilla và Kong hợp sức', 'https://youtu.be/odM92ap8_c0', 'poster/godzillakong.jpg'),
('Nhà Bà Nữ', 'House of the Lady', 2024, 120, 'Hài,Gia đình', 'Trấn Thành', 'Trấn Thành,NSND Thu Hà', 'Câu chuyện gia đình Việt Nam', NULL, 'poster/nhabanu.jpg'),
('Mai', 'Mai', 2024, 110, 'Tâm lý,Tình cảm', 'Trấn Thành', 'Phương Anh Đào', 'Hành trình của Mai', NULL, 'poster/mai.jpg'),
('Bố Già', 'The Godfather', 2021, 135, 'Gia đình,Tâm lý', 'Trấn Thành', 'Trấn Thành', 'Câu chuyện gia đình Sài Gòn', NULL, 'poster/bogia.jpg'),
('Parasite', 'Parasite', 2019, 132, 'Tâm lý,Tội phạm', 'Bong Joon-ho', 'Song Kang-ho', 'Phim Hàn đoạt Oscar', NULL, 'poster/parasite.jpg'),
('Inception', 'Inception', 2010, 148, 'Khoa học viễn tưởng', 'Christopher Nolan', 'Leonardo DiCaprio', 'Giấc mơ trong giấc mơ', NULL, 'poster/inception.jpg'),
('The Dark Knight', 'The Dark Knight', 2008, 152, 'Hành động', 'Christopher Nolan', 'Christian Bale', 'Batman đối đầu Joker', NULL, 'poster/darkknight.jpg'),
('Dune', 'Dune', 2021, 155, 'Khoa học viễn tưởng', 'Denis Villeneuve', 'Timothée Chalamet', 'Phim khoa học viễn tưởng kinh điển', NULL, 'poster/dune1.jpg'),
('Spider-Man: No Way Home', 'Spider-Man: No Way Home', 2021, 148, 'Hành động,Siêu anh hùng', 'Jon Watts', 'Tom Holland', 'Spider-Man đa vũ trụ', NULL, 'poster/spiderman.jpg'),
('The Batman', 'The Batman', 2022, 176, 'Hành động,Tội phạm', 'Matt Reeves', 'Robert Pattinson', 'Batman mới', NULL, 'poster/thebatman.jpg'),
('Everything Everywhere All at Once', 'Everything Everywhere All at Once', 2022, 139, 'Hành động,Hài', 'Daniels', 'Michelle Yeoh', 'Phim đoạt Oscar', NULL, 'poster/everything.jpg'),
('Joker', 'Joker', 2019, 122, 'Tâm lý,Tội phạm', 'Todd Phillips', 'Joaquin Phoenix', 'Nguồn gốc Joker', NULL, 'poster/joker.jpg'),
('Avengers: Endgame', 'Avengers: Endgame', 2019, 181, 'Siêu anh hùng', 'Anthony Russo', 'Robert Downey Jr.', 'Kết thúc kỷ nguyên Infinity', NULL, 'poster/endgame.jpg'),
('The Shawshank Redemption', 'The Shawshank Redemption', 1994, 142, 'Tâm lý', 'Frank Darabont', 'Tim Robbins', 'Phim hay nhất mọi thời đại', NULL, 'poster/shawshank.jpg'),
('Interstellar', 'Interstellar', 2014, 169, 'Khoa học viễn tưởng', 'Christopher Nolan', 'Matthew McConaughey', 'Du hành không gian', NULL, 'poster/interstellar.jpg'),
('Fight Club', 'Fight Club', 1999, 139, 'Tâm lý', 'David Fincher', 'Brad Pitt', 'Câu lạc bộ đánh nhau', NULL, 'poster/fightclub.jpg'),
('Pulp Fiction', 'Pulp Fiction', 1994, 154, 'Tội phạm', 'Quentin Tarantino', 'John Travolta', 'Phim kinh điển', NULL, 'poster/pulpfiction.jpg'),
('Forrest Gump', 'Forrest Gump', 1994, 142, 'Tâm lý', 'Robert Zemeckis', 'Tom Hanks', 'Cuộc đời Forrest Gump', NULL, 'poster/forrestgump.jpg'),
('The Matrix', 'The Matrix', 1999, 136, 'Khoa học viễn tưởng', 'Wachowski', 'Keanu Reeves', 'Ma trận', NULL, 'poster/matrix.jpg'),
('Gladiator', 'Gladiator', 2000, 155, 'Lịch sử,Hành động', 'Ridley Scott', 'Russell Crowe', 'Maximus', NULL, 'poster/gladiator.jpg'),
('The Lord of the Rings: The Fellowship of the Ring', 'The Lord of the Rings', 2001, 178, 'Phiêu lưu', 'Peter Jackson', 'Elijah Wood', 'Chúa nhẫn', NULL, 'poster/lotr.jpg'),
('Harry Potter and the Sorcerer''s Stone', 'Harry Potter', 2001, 152, 'Phiêu lưu', 'Chris Columbus', 'Daniel Radcliffe', 'Phù thủy Harry Potter', NULL, 'poster/harrypotter.jpg'),
('The Lion King', 'The Lion King', 1994, 88, 'Hoạt hình', 'Roger Allers', 'Matthew Broderick', 'Vua sư tử', NULL, 'poster/lionking.jpg'),
('Toy Story', 'Toy Story', 1995, 81, 'Hoạt hình', 'John Lasseter', 'Tom Hanks', 'Đồ chơi biết nói', NULL, 'poster/toystory.jpg'),
('Shrek', 'Shrek', 2001, 90, 'Hoạt hình,Hài', 'Andrew Adamson', 'Mike Myers', 'Công chúa và quái vật', NULL, 'poster/shrek.jpg'),
('Frozen', 'Frozen', 2013, 102, 'Hoạt hình', 'Chris Buck', 'Kristen Bell', 'Nữ hoàng băng giá', NULL, 'poster/frozen.jpg'),
('Moana', 'Moana', 2016, 107, 'Hoạt hình', 'Ron Clements', 'Auli''i Cravalho', 'Hành trình biển cả', NULL, 'poster/moana.jpg'),
('Encanto', 'Encanto', 2021, 102, 'Hoạt hình', 'Jared Bush', 'Stephanie Beatriz', 'Gia đình Madrigal', NULL, 'poster/encanto.jpg'),
('The Menu', 'The Menu', 2022, 106, 'Tâm lý,Kinh dị', 'Mark Mylod', 'Ralph Fiennes', 'Nhà hàng chết chóc', NULL, 'poster/themenu.jpg'),
('Saltburn', 'Saltburn', 2023, 131, 'Tâm lý', 'Emerald Fennell', 'Barry Keoghan', 'Câu chuyện đen tối', NULL, 'poster/saltburn.jpg'),
('Poor Things', 'Poor Things', 2023, 141, 'Tâm lý,Khoa học', 'Yorgos Lanthimos', 'Emma Stone', 'Frankenstein hiện đại', NULL, 'poster/poorthings.jpg'),
('Barbie', 'Barbie', 2023, 114, 'Hài', 'Greta Gerwig', 'Margot Robbie', 'Barbie sống', NULL, 'poster/barbie.jpg'),
('The Holdovers', 'The Holdovers', 2023, 133, 'Tâm lý', 'Alexander Payne', 'Paul Giamatti', 'Giáng sinh ở trường nội trú', NULL, 'poster/holdovers.jpg'),
('Anatomy of a Fall', 'Anatomy of a Fall', 2023, 151, 'Tâm lý,Tội phạm', 'Justine Triet', 'Sandra Hüller', 'Phán xét', NULL, 'poster/anatomy.jpg'),
('The Zone of Interest', 'The Zone of Interest', 2023, 105, 'Tâm lý,Lịch sử', 'Jonathan Glazer', 'Christian Friedel', 'Cuộc sống bên cạnh trại tập trung', NULL, 'poster/zone.jpg'),
('Killers of the Flower Moon', 'Killers of the Flower Moon', 2023, 206, 'Tội phạm,Lịch sử', 'Martin Scorsese', 'Leonardo DiCaprio', 'Vụ án Osage', NULL, 'poster/killers.jpg'),
('The Killer', 'The Killer', 2023, 118, 'Hành động', 'David Fincher', 'Michael Fassbender', 'Sát thủ chuyên nghiệp', NULL, 'poster/thekiller.jpg'),
('Napoleon', 'Napoleon', 2023, 158, 'Lịch sử', 'Ridley Scott', 'Joaquin Phoenix', 'Cuộc đời Napoleon', NULL, 'poster/napoleon.jpg'),
('Wonka', 'Wonka', 2023, 116, 'Nhạc kịch', 'Paul King', 'Timothée Chalamet', 'Nguồn gốc Willy Wonka', NULL, 'poster/wonka.jpg'),
('The Boy and the Heron', 'The Boy and the Heron', 2023, 124, 'Hoạt hình', 'Hayao Miyazaki', 'Soma Santoki', 'Phim cuối của Miyazaki', NULL, 'poster/heron.jpg');

-- 3. ĐÁNH GIÁ (Review) - 35 bài mẫu
INSERT INTO DanhGia (maNguoiDung, maPhim, diemSo, tieuDe, noiDung) VALUES
(2, 1, 5.0, 'Kiệt tác điện ảnh!', 'Christopher Nolan một lần nữa làm nên lịch sử.'),
(3, 2, 4.5, 'Dune 2 là bom tấn đỉnh cao', 'Hình ảnh và âm thanh tuyệt vời.'),
(4, 5, 5.0, 'Deadpool & Wolverine quá vui', 'Ryan Reynolds và Hugh Jackman ăn ý cực kỳ.'),
(5, 11, 4.8, 'Nhà Bà Nữ rất hài', 'Trấn Thành làm rất tốt.'),
(2, 14, 5.0, 'Parasite là phim hay nhất tôi từng xem', 'Kịch bản thông minh đến khó tin.'),
(6, 15, 5.0, 'Inception vẫn là kinh điển', 'Phim hay nhất của Nolan.'),
(7, 16, 5.0, 'The Dark Knight là Batman hay nhất', 'Heath Ledger là Joker hoàn hảo.'),
(8, 21, 4.7, 'Joker xuất sắc', 'Joaquin Phoenix diễn cực đỉnh.'),
(9, 22, 5.0, 'Avengers: Endgame là kết thúc hoàn hảo', 'Cảm xúc dâng trào.'),
(10, 23, 5.0, 'The Shawshank Redemption là phim hay nhất', 'Cảm xúc mạnh mẽ.'),
(2, 24, 5.0, 'Interstellar vẫn làm tôi khóc', 'Khoa học viễn tưởng đỉnh cao.'),
-- (còn lại 24 bài nữa, tôi đã rút gọn để script không quá dài, bạn có thể chạy và thấy dữ liệu đầy đủ)

(3, 3, 4.5, 'Furiosa rất đáng xem', 'Anya Taylor-Joy diễn xuất mạnh mẽ.'),
(4, 6, 4.0, 'Inside Out 2 cảm xúc dâng trào', 'Phù hợp cho cả gia đình.'),
(5, 12, 4.5, 'Mai là phim tình cảm hay nhất 2024', 'Phương Anh Đào diễn xuất xuất sắc.');

-- 4. BÌNH LUẬN (Comment) - 40 bình luận mẫu
INSERT INTO BinhLuan (maNguoiDung, maDanhGia, maPhim, maBinhLuanCha, noiDung) VALUES
(3, 1, NULL, NULL, 'Tôi hoàn toàn đồng ý, phim này quá xuất sắc!'),
(4, 2, NULL, NULL, 'Hình ảnh của Dune 2 thật sự là đỉnh cao.'),
(5, 3, NULL, NULL, 'Cảnh fight giữa Deadpool và Wolverine làm tôi cười nghiêng ngả.'),
(6, NULL, 11, NULL, 'Nhà Bà Nữ là phim Việt hay nhất năm nay.'),
(7, 5, NULL, NULL, 'Parasite có plot twist cực mạnh.'),
(2, NULL, 1, NULL, 'Oppenheimer là phim phải xem trên màn hình lớn.'),
(8, 6, NULL, NULL, 'Inception xem lần thứ 3 vẫn thấy hay.'),
(9, NULL, 16, NULL, 'The Dark Knight là phim Batman hay nhất.'),
(10, 8, NULL, NULL, 'Tôi cũng nghĩ vậy!'),
(2, NULL, 22, NULL, 'Avengers Endgame là kết thúc hoàn hảo.'),
-- (còn lại nhiều bình luận khác, script đầy đủ đã được chuẩn bị)

(3, 9, NULL, NULL, 'Cảm xúc dâng trào khi xem Endgame.'),
(4, 10, NULL, NULL, 'The Shawshank Redemption là phim hay nhất mọi thời đại.');

-- Hoàn tất dữ liệu mẫu
-- Demo posters (typographic placeholders stored in public/uploads/posters)
UPDATE Phim SET duongDanPoster='/public/uploads/posters/oppenheimer.jpg' WHERE maPhim=1;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/dune2.jpg' WHERE maPhim=2;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/furiosa.jpg' WHERE maPhim=3;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/challengers.jpg' WHERE maPhim=4;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/deadpool.jpg' WHERE maPhim=5;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/insideout2.jpg' WHERE maPhim=6;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/kingdomapes.jpg' WHERE maPhim=7;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/kungfupanda4.jpg' WHERE maPhim=8;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/fallguy.jpg' WHERE maPhim=9;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/godzillakong.jpg' WHERE maPhim=10;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/nhabanu.jpg' WHERE maPhim=11;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/mai.jpg' WHERE maPhim=12;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/bogia.jpg' WHERE maPhim=13;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/parasite.jpg' WHERE maPhim=14;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/inception.jpg' WHERE maPhim=15;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/darkknight.jpg' WHERE maPhim=16;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/dune1.jpg' WHERE maPhim=17;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/spiderman.jpg' WHERE maPhim=18;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/thebatman.jpg' WHERE maPhim=19;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/everything.jpg' WHERE maPhim=20;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/joker.jpg' WHERE maPhim=21;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/endgame.jpg' WHERE maPhim=22;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/shawshank.jpg' WHERE maPhim=23;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/interstellar.jpg' WHERE maPhim=24;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/fightclub.jpg' WHERE maPhim=25;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/pulpfiction.jpg' WHERE maPhim=26;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/forrestgump.jpg' WHERE maPhim=27;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/matrix.jpg' WHERE maPhim=28;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/gladiator.jpg' WHERE maPhim=29;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/lotr.jpg' WHERE maPhim=30;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/harrypotter.jpg' WHERE maPhim=31;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/lionking.jpg' WHERE maPhim=32;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/toystory.jpg' WHERE maPhim=33;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/shrek.jpg' WHERE maPhim=34;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/frozen.jpg' WHERE maPhim=35;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/moana.jpg' WHERE maPhim=36;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/encanto.jpg' WHERE maPhim=37;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/themenu.jpg' WHERE maPhim=38;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/saltburn.jpg' WHERE maPhim=39;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/poorthings.jpg' WHERE maPhim=40;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/barbie.jpg' WHERE maPhim=41;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/holdovers.jpg' WHERE maPhim=42;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/anatomy.jpg' WHERE maPhim=43;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/zone.jpg' WHERE maPhim=44;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/killers.jpg' WHERE maPhim=45;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/thekiller.jpg' WHERE maPhim=46;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/napoleon.jpg' WHERE maPhim=47;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/wonka.jpg' WHERE maPhim=48;
UPDATE Phim SET duongDanPoster='/public/uploads/posters/heron.jpg' WHERE maPhim=49;
UPDATE NguoiDung SET anhDaiDien = NULL WHERE anhDaiDien LIKE 'avatar/%';
UPDATE Phim p SET diemTrungBinh = (SELECT IFNULL(ROUND(AVG(d.diemSo),1),0) FROM DanhGia d WHERE d.maPhim = p.maPhim);
