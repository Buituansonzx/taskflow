@component('mail::message')
# Bạn được mời vào workspace

**{{ $invitedBy }}** đã mời bạn tham gia workspace **{{ $workspaceName }}** với vai trò **{{ $role }}**.

@component('mail::button', ['url' => $acceptUrl])
Chấp nhận lời mời
@endcomponent

Link sẽ hết hạn lúc **{{ $expiredAt }}**.

Nếu bạn không muốn tham gia, hãy bỏ qua email này.
@endcomponent