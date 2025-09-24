CREATE TABLE type (
    type_name VARCHAR(100) PRIMARY KEY
);

CREATE TABLE level (
    level_name VARCHAR(100) PRIMARY KEY
);

CREATE TABLE user (
    user_name VARCHAR(100) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    level_name VARCHAR(100),
    type_name VARCHAR(100),
    FOREIGN KEY (level_name) REFERENCES level(level_name) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (type_name) REFERENCES type(type_name) ON DELETE SET NULL ON UPDATE CASCADE
);


CREATE TABLE ingredient (
    ing_name VARCHAR(100) PRIMARY KEY,
    category VARCHAR(100)
);


CREATE TABLE recipe (
    recipe_name VARCHAR(100) PRIMARY KEY,
    time INT, 
    recipe_details TEXT,
    level_name VARCHAR(100),
    type_name VARCHAR(100),
    FOREIGN KEY (level_name) REFERENCES level(level_name) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (type_name) REFERENCES type(type_name) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE recipe_ingredient (
    recipe_name VARCHAR(100),
    ing_name VARCHAR(100),
    PRIMARY KEY (recipe_name, ing_name),
    FOREIGN KEY (recipe_name) REFERENCES recipe(recipe_name) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ing_name) REFERENCES ingredient(ing_name) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE user_ingredient (
    user_name VARCHAR(100),
    ing_name VARCHAR(100),
    PRIMARY KEY (user_name, ing_name),
    FOREIGN KEY (user_name) REFERENCES user(user_name) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ing_name) REFERENCES ingredient(ing_name) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE favorites (
    user_name VARCHAR(100),
    recipe_name VARCHAR(100),
    PRIMARY KEY (user_name, recipe_name),
    FOREIGN KEY (user_name) REFERENCES user(user_name) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (recipe_name) REFERENCES recipe(recipe_name) ON DELETE CASCADE ON UPDATE CASCADE
);
  
-- insert types
INSERT INTO type (type_name) VALUES 
('Healthy'), ('Indulgent'), ('Vegetarian'), ('Cheap recipe');

-- insert levels
INSERT INTO level (level_name) VALUES 
('Easy'), ('Medium'), ('Hard');

-- insert ingredients with correct categories
INSERT INTO ingredient (ing_name, category) VALUES 
('Eggs', 'Meat'),
('Escalope', 'Meat'),
('Poulet', 'Meat'),
('Viande hachée', 'Meat'),
('Thon', 'Meat'),
('Tomatoes', 'Vegetables'),
('Garlic', 'Vegetables'),
('Onion', 'Vegetables'),
('Potatoes', 'Vegetables'),
('Courgettes', 'Vegetables'),
('Salade', 'Vegetables'),
('Lentilles', 'Vegetables'),
('Fromage', 'Laiterie'),
('Milk', 'Laiterie'),
('Butter', 'Laiterie'),
('Yaourt', 'Laiterie'),
('Sucre', 'Daily'),
('Salt', 'Daily'),
('Oil', 'Daily'),
('Epice', 'Daily'),
('Semoule', 'Daily'),
('Vinegar', 'Daily'),
('Riz', 'Pasta'),
('Lasagnes', 'Pasta'),
('Spaghetti', 'Pasta'),
('Couscous', 'Pasta');



-- insert recipes with exact details(wanted to try only two for now plus win rah couscous 5awti )
INSERT INTO recipe (recipe_name, time, recipe_details, level_name, type_name) VALUES
('Pasta', 30, 'Boil a large pot of salted water. Cook the pasta according to package instructions (usually 8–10 minutes). While pasta cooks, heat oil in a pan. Add minced garlic and sauté for 1 minute. Add tomato sauce. Simmer for 10 minutes. Add salt and pepper. Drain the pasta, keeping a little bit of pasta water. Toss the pasta with the sauce. Add a little pasta water if sauce is too thick. Garnish with cheese if desired.', 'Easy', 'Vegetarian'),
('Lasagna', 60, 'Preheat oven to 180°C (350°F). In a pan, cook onion and garlic with oil until soft. Add ground beef and cook until browned. Add tomato sauce, salt, pepper, oregano. Simmer 15 minutes. In a bowl, mix cheese and egg. Cook lasagna sheets according to package (if needed). In a baking dish, layer: sauce → lasagna sheets → ricotta mixture → mozzarella → repeat. Finish with cheese on top. Bake for 30–40 minutes until golden and bubbling.', 'Medium', 'Indulgent'),
('Crepes', 25, 'In a bowl, mix flour and salt (and sugar if sweet). Make a well, add eggs, start mixing. Gradually add milk while whisking to avoid lumps. Add oil or melted butter. Let batter rest 30 minutes. Heat a non-stick pan with a little butter. Pour a thin layer of batter, rotate pan to spread it. Cook about 1–2 minutes per side until golden.', 'Easy', 'Cheap recipe'),
('Macédoine', 20, 'Boil diced carrots, potatoes separately until tender. Drain and let cool. In a bowl, mix the cooked vegetables. Add salt, pepper, and enough mayonnaise to coat. Cook the rice then let it cool to add it to the vegetables. Add olives and corn. Chill before serving.', 'Easy', 'Healthy'),
('Les Tartes', 40, 'Pre-bake the pie crust until golden. Prepare pastry cream (or buy ready). Spread pastry cream over cooled crust. Decorate with sliced fruits neatly.', 'Medium', 'Indulgent'),
('Mahajeb', 35, 'Mix semolina with salt and water to make a soft dough. Let it rest 30 minutes. In a pan, sauté onions, tomatoes, and pepper with oil and spices. Divide dough into balls. Roll out thinly, place filling in center, fold into square shape. Cook on a hot flat pan until golden on both sides.', 'Medium', 'Cheap recipe'),
('Lentils', 45, 'Rinse lentils well. In a pot, sauté onion and garlic in oil. Add carrots and lentils. Pour water or broth to cover by 3–4 cm. Simmer for 30–40 minutes until lentils are tender. Season with salt and pepper.', 'Easy', 'Healthy'),
('Pancakes', 20, 'In one bowl, mix flour, sugar, baking powder, baking soda, salt. In another bowl, whisk milk, egg, melted butter, and vanilla. Pour wet ingredients into dry ingredients and mix gently (don''t overmix, a few lumps are fine). Let the batter rest for 5 minutes. Heat a pan or griddle over medium heat, lightly butter it. Pour a small ladle of batter; cook until bubbles appear on the surface. Flip and cook the other side until golden. Serve warm with syrup, fruits, or chocolate.', 'Easy', 'Indulgent');

-- recipe_ingredient 

INSERT INTO recipe_ingredient (recipe_name, ing_name) VALUES
-- Pasta
('Pasta', 'Spaghetti'),
('Pasta', 'Oil'),
('Pasta', 'Garlic'),
('Pasta', 'Tomatoes'),
('Pasta', 'Salt'),

-- Lasagna
('Lasagna', 'Lasagnes'),
('Lasagna', 'Viande hachée'),
('Lasagna', 'Onion'),
('Lasagna', 'Garlic'),
('Lasagna', 'Tomatoes'),
('Lasagna', 'Fromage'),
('Lasagna', 'Eggs'),
('Lasagna', 'Oil'),

-- Crepes
('Crepes', 'Eggs'),
('Crepes', 'Milk'),
('Crepes', 'Salt'),
('Crepes', 'Sucre'),
('Crepes', 'Oil'),

-- Macédoine
('Macédoine', 'Potatoes'),
('Macédoine', 'Salt'),
('Macédoine', 'Riz'),

-- Mahajeb
('Mahajeb', 'Semoule'),
('Mahajeb', 'Onion'),
('Mahajeb', 'Tomatoes'),
('Mahajeb', 'Oil'),
('Mahajeb', 'Salt'),

-- Lentils
('Lentils', 'Lentilles'),
('Lentils', 'Onion'),
('Lentils', 'Oil'),
('Lentils', 'Salt'),

-- Pancakes
('Pancakes', 'Eggs'),
('Pancakes', 'Milk'),
('Pancakes', 'Salt'),
('Pancakes', 'Sucre'),
('Pancakes', 'Butter');

  