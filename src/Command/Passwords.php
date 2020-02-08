<?php
declare(strict_types=1);

namespace App\Command;

use App\Mailer\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Passwords extends Command
{
    private const URL = 'https://www.freel2tpvpn.com';
    private const FILE = __DIR__ . '/../../var/passwords.txt';

    protected const NAME = 'app:passwords';
    protected const DESCRIPTION = 'check passwords from www.freel2tpvpn.com';

    /** @var Mailer */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        parent::__construct(null);
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $page = $this->curl(self::URL);

        $pattern = '/[^\n]*Password[^\n]*/';

        preg_match_all($pattern, $page, $matches, PREG_OFFSET_CAPTURE);

        $passwords = [];
        foreach ($matches[0] as $match) {
            $passwords[] = ltrim(rtrim(strip_tags($match[0])));
        }

        $oldPasswords = $this->readPasswords();
        if ($oldPasswords !== json_encode($passwords)) {
            $this->savePasswords(json_encode($passwords));
            $emails = explode(',', $_ENV['EMAILS']);
            $subject = $_ENV['MESSAGE_SUBJECT'];
            $msg = '';
            foreach ($passwords as $password) {
                $msg .= sprintf('%s%s', $password, PHP_EOL);
            }

            foreach ($emails as $email) {
                $this->mailer->sendMessage($email, $subject, $msg);
            }
        }

        return 0;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    private function curl(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function readPasswords(): string
    {
        if (file_exists(self::FILE)) {
            $file = fopen(self::FILE, 'r');
            $passwords = fread($file, 1000);
            fclose($file);
            return $passwords;
        }

        return '';
    }

    private function savePasswords(string $passwords): void
    {
        $file = fopen(self::FILE, 'w');
        fwrite($file, $passwords);
        fclose($file);
    }
}
