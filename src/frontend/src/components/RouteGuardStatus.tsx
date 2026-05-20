type RouteGuardStatusProps = {
  message: string
}

export default function RouteGuardStatus({ message }: RouteGuardStatusProps) {
  return (
    <section className="route-guard-status">
      <p>{message}</p>
    </section>
  )
}
