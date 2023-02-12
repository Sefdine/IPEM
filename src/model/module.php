<?php 

declare(strict_types = 1);

namespace Ipem\Src\Model;

use Ipem\Src\Lib\Database;

trait Module
{
    public function getModules(string $identifier, string $level, string $group): array
    {
        $connection = new Database;
        $statement = $connection->getConnection()->prepare(
            'SELECT m.name 
            FROM modules m, teachs t, levels l, groupes g
            WHERE m.id = t.module_id
            AND m.level_id = l.id
            AND l.group_id = g.id
            AND t.teacher_id = ?
            AND l.level = ?
            AND g.name = ?'
        );
        $statement->execute([$identifier, $level, $group]);
        $modules = [];
        while($row = $statement->fetch()) {
            $modules[] = $row['name'];
        }

        return $modules;
    }

    public function getModulesStudent(int $identifier, string $year): array
    {
        $connection = new Database;
        $statement = $connection->getConnection()->prepare(
            'SELECT m.name FROM modules m
            JOIN levelsmodules lm ON m.id = lm.module_id
            JOIN levels l ON l.id = lm.level_id
            JOIN contain c ON l.id = c.level_id
            JOIN years y ON y.id = c.year_id
            JOIN registrations r ON c.id = r.contain_id
            JOIN students s ON s.id = r.student_id
            JOIN users u ON u.id = s.user_id
            AND u.id = ?
            AND y.name = ?'
        );
        $statement->execute([$identifier, $year]);
        $modules = [];
        while($row = $statement->fetch()) {
            $modules[] = $row['name'];
        }
        return $modules;
    }

    public function getIdModule(string $name, int $id_level): int
    {
        $connection = new Database;
        $statement = $connection->getConnection()->prepare(
            'SELECT id FROM modules WHERE name = ? AND level_id = ?'
        );
        $statement->execute([$name, $id_level]);
        
        return ($row = $statement->fetch()) ? $row['id'] : 0;
    }
}