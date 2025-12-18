-- schema.sql
-- Board Game Cafe Database Schema
-- DB名: boardgamedb（※DB自体の作成は別途）

-- ====================
-- admins
-- ====================
CREATE TABLE admins (
  id bigint(20) NOT NULL,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  created_at datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE admins
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY username (username);

ALTER TABLE admins
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

-- ====================
-- users
-- ====================
CREATE TABLE users (
  id bigint(20) NOT NULL,
  name varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  age int(11) DEFAULT NULL,
  created_at datetime DEFAULT current_timestamp(),
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);

ALTER TABLE users
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

-- ====================
-- games
-- ====================
CREATE TABLE games (
  id bigint(20) NOT NULL,
  title varchar(100) NOT NULL,
  description text DEFAULT NULL,
  genre varchar(50) DEFAULT NULL,
  min_players int(11) DEFAULT NULL,
  max_players int(11) DEFAULT NULL,
  difficulty varchar(20) DEFAULT NULL,
  play_time varchar(20) DEFAULT NULL,
  image_url text DEFAULT NULL,
  created_at datetime DEFAULT current_timestamp(),
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE games
  ADD PRIMARY KEY (id);

ALTER TABLE games
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

-- ====================
-- lendings
-- ====================
CREATE TABLE lendings (
  id bigint(20) NOT NULL,
  user_id bigint(20) NOT NULL,
  game_id bigint(20) NOT NULL,
  lendings_date date NOT NULL,
  due_date date NOT NULL,
  returned_date date DEFAULT NULL,
  status varchar(20) DEFAULT 'lending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE lendings
  ADD PRIMARY KEY (id),
  ADD KEY fk_lending_user (user_id),
  ADD KEY fk_lending_game (game_id);

ALTER TABLE lendings
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

-- ====================
-- reservations
-- ====================
CREATE TABLE reservations (
  id bigint(20) NOT NULL,
  user_id bigint(20) NOT NULL,
  game_id bigint(20) NOT NULL,
  reservation_date date NOT NULL,
  status varchar(20) DEFAULT 'reserved',
  created_at datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE reservations
  ADD PRIMARY KEY (id),
  ADD KEY fk_res_user (user_id),
  ADD KEY fk_res_game (game_id);

ALTER TABLE reservations
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

-- ====================
-- reviews
-- ====================
CREATE TABLE reviews (
  id bigint(20) NOT NULL,
  user_id bigint(20) NOT NULL,
  game_id bigint(20) NOT NULL,
  rating int(11) DEFAULT NULL CHECK (rating BETWEEN 1 AND 5),
  comment text DEFAULT NULL,
  created_at datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE reviews
  ADD PRIMARY KEY (id),
  ADD KEY fk_review_user (user_id),
  ADD KEY fk_review_game (game_id);

ALTER TABLE reviews
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

-- ====================
-- Foreign Keys
-- ====================
ALTER TABLE lendings
  ADD CONSTRAINT fk_lending_user
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_lending_game
    FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE;

ALTER TABLE reservations
  ADD CONSTRAINT fk_res_user
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_res_game
    FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE;

ALTER TABLE reviews
  ADD CONSTRAINT fk_review_user
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_review_game
    FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE;
