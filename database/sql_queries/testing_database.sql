use blog;

DESCRIBE user;
INSERT INTO user(username, password, email, is_admin) 
VALUES('yahya199', 'password', 'yahyalimouni02@gmail.com', 0);

INSERT INTO category(id, name, description)
VALUES('TOCB', 'Tour por Cordoba', 'Un recorrido por la ciudad de Córdoba, Argentina.');

SELECT * FROM categories;
INSERT INTO post(user_id, category_id, title, description, content)
VALUES(1, 'TOCB', 'Un recorrido por la ciudad de Córdoba', 'Un recorrido por la ciudad de Córdoba, Argentina.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');

DELETE FROM post WHERE id = 1;
SELECT * FROM users;

DESCRIBE categories;
SELECT * FROM categories;
SELECT * FROM users;

UPDATE categories SET description="Activity category" WHERE id="ACTS";

UPDATE users SET is_admin = true WHERE username ="yahya197";

DESCRIBE categories;
DESCRIBE posts;
DESCRIBE users;
DESCRIBE post_images;


INSERT INTO users (name, email, password, created_at, updated_at)
VALUES (
  'yahya',
  'yahyalimouni02@gmail.com',
  SHA2('password', 256),
  NOW(),
  NOW()
);

describe profile_images;
