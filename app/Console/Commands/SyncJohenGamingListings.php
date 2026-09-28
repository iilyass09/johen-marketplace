<?php

namespace App\Console\Commands;

use App\Services\JohenGamingSyncService;
use Illuminate\Console\Command;

class SyncJohenGamingListings extends Command
{
    protected $signature = 'jba:sync-johengaming
        {--game= : Hanya sinkron satu game (ml|pubg|fc_mobile|ff|roblox|valorant|efootball)}
        {--dry-run : Tampilkan ringkasan tanpa menulis ke database}
        {--no-images : Jangan unduh gambar}
        {--force-images : Unduh ulang gambar meski sudah ada}
        {--deactivate-missing : Nonaktifkan listing sumber yang tak lagi tampil}
        {--pause= : Jeda antar fetch dalam milidetik (default 100)}';

    protected $description = 'Sinkronkan listing akun dari johengaming.id ke account_listings';

    public function handle(): int
    {
        $service = new JohenGamingSyncService;

        $game = $this->option('game');
        if ($game && $game !== 'all') {
            $this->info("Mulai sinkronisasi game: {$game}");
        } else {
            $this->info('Mulai sinkronisasi semua game dari johengaming.id...');
        }

        $result = $service->sync($game ?: null, [
            'no_images' => (bool) $this->option('no-images'),
            'force_images' => (bool) $this->option('force-images'),
            'deactivate_missing' => (bool) $this->option('deactivate-missing'),
            'dry_run' => (bool) $this->option('dry-run'),
            'pause_ms' => max(0, (int) $this->option('pause')),
        ]);

        $rows = [];
        foreach ($result['games'] as $slug => $g) {
            $rows[] = [$slug, $g['created'], $g['updated'], $g['unchanged'], $g['sold'], $g['errors']];
        }

        $this->table(
            ['Game', 'Dibuat', 'Diperbarui', 'Tidak berubah', 'Terjual', 'Gagal'],
            $rows
        );

        $failed = $result['failed'];
        foreach (array_slice($failed, 0, 20) as $message) {
            $this->error("  - {$message}");
        }
        if (count($failed) > 20) {
            $this->error('  ... dan '.count($failed).' lainnya.');
        }

        $suffix = $this->option('dry-run') ? ' (dry-run, tidak disimpan)' : '';
        $this->info(sprintf(
            'Ringkasan%s: %d dibuat, %d diperbarui, %d tidak berubah, %d ditandai terjual, %d gagal.',
            $suffix,
            $result['created'],
            $result['updated'],
            $result['unchanged'],
            $result['sold'],
            $result['errors']
        ));

        return $result['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
