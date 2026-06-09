<?php
namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function create($type, $title, $message, $url = null, $userId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'is_read' => false
        ]);
    }

    public static function notifyRiskCreated($risk)
    {
        return self::create(
            'risk_created',
            'Risiko Baru',
            "Risiko '{$risk->risk_nama}' telah ditambahkan",
            route('risks.edit', $risk->risk_id)
        );
    }

    public static function notifyRiskUpdated($risk)
    {
        return self::create(
            'risk_updated',
            'Risiko Diupdate',
            "Risiko '{$risk->risk_nama}' telah diupdate",
            route('risks.edit', $risk->risk_id)
        );
    }

    public static function notifyProjectCreated($project)
    {
        return self::create(
            'project_created',
            'Proyek Baru',
            "Proyek '{$project->pro_nama}' telah ditambahkan",
            route('projects.edit', $project->pro_id)
        );
    }

    public static function notifyHighRisk($risk)
    {
        return self::create(
            'high_risk_alert',
            '⚠️ Risiko Tinggi Terdeteksi',
            "Risiko '{$risk->risk_nama}' memiliki level TINGGI dan memerlukan perhatian segera!",
            route('risks.edit', $risk->risk_id)
        );
    }
}