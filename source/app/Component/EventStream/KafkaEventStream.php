<?php

namespace App\Component\EventStream;

use Illuminate\Support\Facades\Log;
use Rapide\LaravelQueueKafka\Queue\KafkaQueue;
use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\KafkaConsumer;
use RdKafka\TopicConf;

class KafkaEventStream
{
    /**
     * @var \RdKafka\Producer
     */
    protected $producer;

    /**
     * @var \RdKafka\KafkaConsumer
     */
    protected $consumer;

    /**
     * @var \RdKafka\Conf
     */
    protected $conf;

    /**
     * @var \Laravel\Lumen\Application|mixed
     */
    protected $container;

    /**
     * @var KafkaQueue
     */
    protected $queue;

    /**
     * @var array
     */
    protected $queueConfig = [
        'sleep_error' => true,
    ];

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var string
     */
    protected $queueBrokers;

    /**
     * @var string
     */
    protected $consumerGroupId;

    /**
     * KafkaEventStream constructor.
     */
    public function __construct()
    {
        $this->container = app();

        $this->setQueueName();
        $this->setQueueBrokers();
        $this->setConsumerGroupId();
        $this->conf = new Conf();
        $this->conf->set('group.id', $this->getConsumerGroupId());
        // Initial list of Kafka brokers
        $this->conf->set('metadata.broker.list', $this->getQueueBrokers());
        /** @var RdKafka\Producer producer */
        $this->producer = new Producer();
        $this->producer->addBrokers($this->getQueueBrokers());
        $topicConf = new TopicConf();
        $topicConf->set('auto.offset.reset', 'latest');
        $this->producer->newTopic($this->getQueueName(), $topicConf);
        $this->consumer = new KafkaConsumer($this->conf);
        $this->consumer->subscribe([$this->getQueueName()]);
        $this->queueConfig += [
            'queue' => $this->getQueueName(),
        ];
        $this->queue = new KafkaQueue($this->producer, $this->consumer, $this->queueConfig);
        $this->queue->setContainer($this->container);
    }

    /**
     * @return KafkaQueue
     */
    public function getQueue(): KafkaQueue
    {
        return $this->queue;
    }

    /**
     * @return Producer
     */
    public function getProducer(): Producer
    {
        return $this->producer;
    }

    /**
     * @return KafkaConsumer
     */
    public function getConsumer(): KafkaConsumer
    {
        return $this->consumer;
    }

    /**
     * Get the configured Kafka queue name.
     *
     * @return string
     */
    protected function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @param null|string $queueName
     */
    protected function setQueueName($queueName = null): void
    {
        $this->queueName = $queueName
            ? $queueName
            : config('queue.connections.kafka.queue');
    }

    /**
     * Get the configured Kafka brokers.
     *
     * @return string
     */
    protected function getQueueBrokers()
    {
        return $this->queueBrokers;
    }

    /**
     * @param null|string $queueBrokers
     */
    public function setQueueBrokers($queueBrokers = null): void
    {
        $this->queueBrokers = $queueBrokers
            ? $queueBrokers
            : config('queue.connections.kafka.brokers');
    }

    /**
     * @return string
     */
    public function getConsumerGroupId()
    {
        return $this->consumerGroupId;
    }

    /**
     * @param null|string $groupId
     */
    public function setConsumerGroupId($consumerGroupId = null): void
    {
        $this->consumerGroupId = $consumerGroupId
            ? $consumerGroupId
            : config('queue.connections.kafka.consumer_group_id');
    }
}
