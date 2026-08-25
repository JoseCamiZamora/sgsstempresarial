<?php
namespace App\Enums;
enum CandidateStatus: string { case REGISTERED = 'registered'; case APPROVED = 'approved'; case REJECTED = 'rejected'; case WITHDRAWN = 'withdrawn'; }
