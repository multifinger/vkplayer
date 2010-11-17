CREATE TABLE playlist (id BIGINT AUTO_INCREMENT, name VARCHAR(255) NOT NULL, vk_user_id BIGINT, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE playlist_item (id BIGINT AUTO_INCREMENT, playlist_id BIGINT NOT NULL, title VARCHAR(255), author VARCHAR(255), mp3 VARCHAR(255), time BIGINT, INDEX playlist_id_idx (playlist_id), PRIMARY KEY(id)) ENGINE = INNODB;
ALTER TABLE playlist_item ADD CONSTRAINT playlist_item_playlist_id_playlist_id FOREIGN KEY (playlist_id) REFERENCES playlist(id) ON DELETE CASCADE;
