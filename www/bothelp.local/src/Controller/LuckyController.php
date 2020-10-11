<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class LuckyController
{
    public function number(): Response
    {
        $number = random_int(0, 100);
        
        $this
                ->get('old_sound_rabbit_mq.tasks_producer')
                ->publish('Сообщение...');

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}