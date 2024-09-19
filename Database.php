<?php

class Database
{
    private $pdo;

    public function __construct($host, $dbname, $user, $password)
    {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function insertFont($fontName, $filePath)
    {
        $stmt = $this->pdo->prepare("INSERT INTO fonts (font_name, file_path) VALUES (:font_name, :file_path)");
        $stmt->bindParam(':font_name', $fontName);
        $stmt->bindParam(':file_path', $filePath);
        return $stmt->execute();
    }

    public function getAllFonts()
    {
        $query = "SELECT * FROM fonts";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFont($fontId)
    {
        $query = $this->pdo->prepare("DELETE FROM fonts WHERE id = :id");
        $query->bindParam(':id', $fontId, PDO::PARAM_INT);
        return $query->execute();
    }

    public function showFont($fontId)
    {
        $query = $this->pdo->prepare("SELECT file_path FROM fonts WHERE id = :id");
        $query->bindParam(':id', $fontId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function createFontGroup($title, $fontIds, $fontTitles)
    {
        $query = $this->pdo->prepare("INSERT INTO font_groups (title) VALUES (:title)");
        $query->execute(['title' => $title]);
        $fontGroupId = $this->pdo->lastInsertId();

        $query = $this->pdo->prepare("INSERT INTO font_groups_items (font_id, font_group_id, font_title) VALUES (:font_id, :font_group_id, :font_title)");

        foreach ($fontIds as $index => $fontId) {
            $fontTitle = $fontTitles[$index];
            $query->execute([
                'font_id' => $fontId,
                'font_group_id' => $fontGroupId,
                'font_title' => $fontTitle
            ]);
        }

        return true;
    }

    public function fetchFontGroups()
    {
        $query = "
                    SELECT fg.id, fg.title, COUNT(fgi.font_id) AS font_count
                    FROM font_groups fg
                    LEFT JOIN font_groups_items fgi ON fg.id = fgi.font_group_id
                    GROUP BY fg.id, fg.title
                ";

        $statement = $this->pdo->query($query);
        $fontGroups = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fontGroups as &$group) {
            $query = "
                        SELECT f.font_name
                        FROM fonts f
                        INNER JOIN font_groups_items fgi ON f.id = fgi.font_id
                        WHERE fgi.font_group_id = :font_group_id
                    ";
            $statement = $this->pdo->prepare($query);
            $statement->execute(['font_group_id' => $group['id']]);
            $group['fonts'] = $statement->fetchAll(PDO::FETCH_COLUMN);
        }

        return $fontGroups;
    }

    public function deleteFontGroup($groupId)
    {
        $query = $this->pdo->prepare("DELETE FROM font_groups_items WHERE font_group_id = :id");
        $query->execute(['id' => $groupId]);

        $query = $this->pdo->prepare("DELETE FROM font_groups WHERE id = :id");
        return $query->execute(['id' => $groupId]);
    }

    public function getFontGroupById($groupId)
    {
        $query = $this->pdo->prepare("SELECT * FROM font_groups WHERE id = :id");
        $query->execute(['id' => $groupId]);
        $group = $query->fetch(PDO::FETCH_ASSOC);

        $fontsQuery = $this->pdo->prepare("
                            SELECT fgi.*, f.font_name 
                            FROM font_groups_items fgi
                            JOIN fonts f ON fgi.font_id = f.id
                            WHERE fgi.font_group_id = :id
                        ");

        $fontsQuery->execute(['id' => $groupId]);
        $fonts = $fontsQuery->fetchAll(PDO::FETCH_ASSOC);

        if ($group) {
            $group['fonts'] = $fonts;
        }

        return $group;
    }

    public function updateFontGroup($groupId, $title, $fontIds, $fontTitles)
    {
        $query = $this->pdo->prepare("UPDATE font_groups SET title = :title WHERE id = :id");
        $query->execute(['title' => $title, 'id' => $groupId]);

        $this->pdo->prepare("DELETE FROM font_groups_items WHERE font_group_id = :id")->execute(['id' => $groupId]);

        $query = $this->pdo->prepare("INSERT INTO font_groups_items (font_id, font_group_id, font_title) VALUES (:font_id, :font_group_id, :font_title)");
        foreach ($fontIds as $index => $fontId) {
            $fontTitle = $fontTitles[$index];
            $query->execute([
                'font_id' => $fontId,
                'font_group_id' => $groupId,
                'font_title' => $fontTitle
            ]);
        }

        return true;
    }
}
