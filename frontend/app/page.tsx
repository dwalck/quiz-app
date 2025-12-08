import { Button } from "@/components/ui/button"
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {Field, FieldLabel} from "@/components/ui/field";

export default function Home() {
  return (
      <div className="chat-init">
          <Card className="chat-init__card">
              <div className="chat-init__card__row">

                  <h2 className={"scroll-m-20 pb-2 text-3xl font-semibold tracking-tight first:mt-0"}>Logowanie</h2>

                  <Field>
                      <FieldLabel htmlFor="checkout-7j9-card-name-43j">
                          Name on Card
                      </FieldLabel>
                      <Input
                          id="checkout-7j9-card-name-43j"
                          placeholder="Evil Rabbit"
                          required
                      />
                  </Field>

              </div>
          </Card>
      </div>
  )
}
