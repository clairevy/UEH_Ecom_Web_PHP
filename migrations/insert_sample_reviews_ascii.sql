-- Insert sample reviews với tiếng Việt đơn giản hơn
SET NAMES utf8 COLLATE utf8_unicode_ci;

DELETE FROM product_reviews;

INSERT INTO product_reviews (product_id, user_id, rating, title, comment, status, created_at) VALUES
(1, 1, 5, 'Nhan rat dep, kim cuong lap lanh', 'Nhan rat dep, kim cuong lap lanh, chat luong tuyet voi. Giao hang nhanh, dong goi can than.', 'approved', '2025-10-01 15:15:54'),
(1, 2, 4, 'Thiet ke sang trong', 'Thiet ke sang trong, phu hop voi nhieu trang phuc. Rat hai long voi su lua chon nay.', 'approved', '2025-09-28 15:15:54'),
(1, 3, 5, 'Tuyet voi!', 'San pham vuot mong doi, chat luong tuyet voi!', 'approved', '2025-09-26 15:15:54'),
(1, 4, 3, 'Tot nhung hoi dat', 'San pham tot nhung hoi dat so voi chat luong. Minh se can nhac mua lan sau.', 'approved', '2025-09-23 15:15:54'),

(2, 4, 4, 'Day chuyen rat dep', 'Day chuyen rat dep va tinh te, phu hop cho nhieu dip. Chat luong tot.', 'approved', '2025-10-02 15:15:54'),
(2, 1, 5, 'Qua dep luon!', 'Minh rat hai long voi day chuyen nay, deo len rat dep va noi bat.', 'approved', '2025-09-30 15:15:54'),
(2, 6, 3, 'Tot nhung nho', 'San pham tot nhung hoi nho so voi mong doi cua minh.', 'approved', '2025-09-27 15:15:54'),
(2, 3, 2, 'Khong hai long', 'San pham khong nhu hinh anh mo ta tren web.', 'approved', '2025-09-21 15:15:54'),

(3, 2, 5, 'Bong tai tuyet dep', 'Bong tai thiet ke rat dep, phu hop voi nhieu kieu toc khac nhau.', 'approved', '2025-09-29 15:15:54'),
(3, 3, 4, 'Chat luong tuyet voi', 'Chat luong tuyet voi, san pham rat dep va tinh te. Rat hai long.', 'approved', '2025-09-25 15:15:54'),

(4, 4, 5, 'Vong tay rat dep va ung y!', 'Vong tay rat dep va ung y, chat luong tuyet voi. Kim cuong dep.', 'approved', '2025-10-02 15:15:54'),
(4, 6, 4, 'Hai long voi su lua chon', 'San pham tot, nhung hoi ton thoi gian giao hang. Noi chung thi hai long.', 'approved', '2025-09-24 15:15:54'),

(5, 1, 5, 'Cuc ky hai long voi san pham', 'Cuc ky hai long voi san pham nay, chat luong sang trong. Minh rat khuyen khich mua.', 'approved', '2025-10-01 15:15:54'),
(5, 2, 4, 'Hang chat luong tot', 'Chat luong tuyet voi, thiet ke dep va thoai mai khi deo. Rat hai long.', 'approved', '2025-09-28 15:15:54'),

-- Them mot vai review chua duyet
(3, 1, 4, 'Dep nhung hoi nho', 'Bong tai thiet ke dep nhung kich thuoc nho hon toi tuong.', 'pending', '2025-10-03 15:15:54'),
(4, 2, 5, 'Perfect!', 'Hoan hao! Qua hai long! Se gioi thieu cho ban be luon.', 'pending', '2025-10-03 15:15:54');