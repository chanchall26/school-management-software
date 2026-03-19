<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <x-panel.stat-card
        icon="users"
        :value="$totalUsers"
        label="Total Users"
        sub="All registered users"
        :trend="'+' . $totalUsers"
        trend_direction="up"
        color="teal"
    />

    <x-panel.stat-card
        icon="check-shield"
        :value="$activeUsers"
        label="Active Users"
        sub="Currently active accounts"
        :trend="'+' . $activeUsers"
        trend_direction="up"
        color="green"
    />

    <x-panel.stat-card
        icon="shield-exclamation"
        :value="$lockedUsers"
        label="Locked Accounts"
        sub="Accounts currently locked"
        :trend="$lockedUsers > 0 ? '+' . $lockedUsers : '0'"
        trend_direction="{{ $lockedUsers > 0 ? 'down' : 'neutral' }}"
        color="red"
    />

    <x-panel.stat-card
        icon="users"
        :value="$teacherCount"
        label="Teachers"
        sub="Users with teacher role"
        :trend="'+' . $teacherCount"
        trend_direction="up"
        color="blue"
    />

</div>
