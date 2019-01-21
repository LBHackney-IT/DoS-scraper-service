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
     * An Rdkafka producer object.
     *
     * @var \RdKafka\Producer
     */
    protected $producer;

    /**
     * An Rdkafka consumer object.
     *
     * @var \RdKafka\KafkaConsumer
     */
    protected $consumer;

    /**
     * An Rdkafka configuration object.
     *
     * @var \RdKafka\Conf
     */
    protected $conf;

    /**
     * Application container.
     *
     * @var \Laravel\Lumen\Application|mixed
     */
    protected $container;

    /**
     * A KafkaQueue object.
     *
     * @var KafkaQueue
     */
    protected $queue;

    /**
     * A key:value queue configuration array.
     *
     * @var array
     */
    protected $queueConfig = [
        'sleep_error' => true,
    ];

    /**
     * The default Kafka queue name.
     *
     * @var string
     */
    protected $queueName;

    /**
     * A string of Kafka queue brokers.
     *
     * @var string
     */
    protected $queueBrokers;

    /**
     * Kafka consumer group ID.
     *
     * @var string
     */
    protected $consumerGroupId;

    /**
     * KafkaEventStream constructor.
     *
     * @param array $queueConfig - A key:value array of queue configurations
     */
    public function __construct($queueConfig = [])
    {
        $this->container = app();

        // Add any queue config supplied.
        $this->queueConfig += $queueConfig;

        $this->setQueueName();
        $this->setQueueBrokers();
        $this->setConsumerGroupId();
        /** @var \RdKafka\Conf conf */
        $this->conf = $this->container->makeWith('queue.kafka.conf');
        $this->conf->set('group.id', $this->getConsumerGroupId());
        // Initial list of Kafka brokers
        $this->conf->set('metadata.broker.list', $this->getQueueBrokers());
        /** @var RdKafka\Producer producer */
        $this->producer = $this->container->makeWith('queue.kafka.producer');
        $this->producer->addBrokers($this->getQueueBrokers());
        /** @var RdKafka\TopicConf $topicConf */
        $topicConf = $this->container->makeWith('queue.kafka.topic_conf');
        $topicConf->set('auto.offset.reset', 'latest');
        $this->producer->newTopic($this->getQueueName(), $topicConf);
//        $this->consumer = new KafkaConsumer($this->conf);
        $this->consumer = $this->container->makeWith('queue.kafka.consumer', ['conf' => $this->conf]);
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
