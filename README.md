# async-anticaptcha
React asynchronous captcha resolver.

        $this->loop = \React\EventLoop\Factory::create();

        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $this->loop);

        $factory = new HttpClient\Factory();

        $resolver = new Anticaptcha\Resolver(
            new Anticaptcha\Service\Antigate(
                $factory->create($this->loop, $dnsResolver), $this->loop, 'your service key'
            )
        );

        $resolver->resolve(
            new Anticaptcha\Captcha(
                file_get_contents(__DIR__ . '/../tests/captcha/343111.jpg')
            )
        )->then(function ($code) {
            var_dump($code);
        })->then(null, function (\Exception $e) {
            var_dump($e->getMessage());
        });


        $loop->run();
