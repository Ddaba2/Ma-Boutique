<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $files = glob($backupDir . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'nom'    => basename($file),
                'taille' => $this->formatSize(filesize($file)),
                'date'   => date('d/m/Y H:i', filemtime($file)),
                'path'   => $file,
            ];
        }

        usort($backups, fn($a, $b) => strcmp($b['nom'], $a['nom']));

        return view('sauvegardes.index', compact('backups'));
    }

    public function store()
    {
        Artisan::call('backup:database');
        $output = Artisan::output();

        if (str_contains($output, 'succès') || str_contains($output, 'success')) {
            return redirect()->route('sauvegardes.index')
                ->with('success', 'Sauvegarde créée avec succès.');
        }

        return redirect()->route('sauvegardes.index')
            ->with('success', 'Sauvegarde lancée. Vérifiez la liste dans quelques secondes.');
    }

    public function download(Request $request)
    {
        $request->validate(['fichier' => 'required|string']);
        $path = storage_path('app/backups/' . basename($request->fichier));

        if (!file_exists($path)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        return response()->download($path);
    }

    public function destroy(Request $request)
    {
        $request->validate(['fichier' => 'required|string']);
        $path = storage_path('app/backups/' . basename($request->fichier));

        if (file_exists($path)) {
            unlink($path);
        }

        return redirect()->route('sauvegardes.index')
            ->with('success', 'Sauvegarde supprimée.');
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' Mo';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' Ko';
        return $bytes . ' o';
    }
}
