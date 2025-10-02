<?php

namespace Litebase;

enum QueryStreamMessageType: int
{
    case OPEN_CONNECTION = 0x01;
    case CLOSE_CONNECTION = 0x02;
    case ERROR = 0x03;
    case FRAME = 0x04;
    case FRAME_ENTRY = 0x05;
}
