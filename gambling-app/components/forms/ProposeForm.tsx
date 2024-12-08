"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import Image from "next/image";
import { useState } from "react";
import { DefaultValues, FieldValues, Path, SubmitHandler, useForm } from "react-hook-form";
import { z, ZodType } from "zod";

import { Button } from "@/components/ui/button";
import { Form, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { useToast } from "@/hooks/use-toast";
import { User } from "@/lib/users";

interface ProposeFormProps<T extends FieldValues> {
  schema: ZodType<T>;
  defaultValues: T;
  users: User[];
  bearer: string;
}
interface ProposeReport {
  message: string;
  code: number;
  condition: number;
  cities?: string[];
}
interface ProposeApiResponse {
  success: boolean;
  data: ProposeReport;
}

const ProposeForm = <T extends FieldValues>({ schema, defaultValues, users, bearer }: ProposeFormProps<T>) => {
  const { toast } = useToast();

  const form = useForm<z.infer<typeof schema>>({
    resolver: zodResolver(schema),
    defaultValues: defaultValues as DefaultValues<T>,
  });

  const [message, setMessage] = useState<string>("");
  const [cities, setCities] = useState<string[] | null>(null);
  const [imageIcon, setImageIcon] = useState<string | null>(null);
  const handleSubmit: SubmitHandler<T> = async () => {
    console.log(form.getValues(), "addsa");

    const myHeaders: Headers = new Headers();
    myHeaders.append("Content-Type", "application/json");
    myHeaders.append("Authorization", `Bearer ${bearer}`);

    const requestOptions: RequestInit = {
      method: "GET",
      headers: myHeaders,

      redirect: "follow",
    };

    fetch(`http://localhost/wp-json/gambling_api/v1/weather_suggestion/${parseInt(form.getValues().user_id)}`, requestOptions)
      .then((response) => response.json())
      .then((result: ProposeApiResponse) => {
        const data: ProposeReport = result.data;

        if (data.code === 1) {
          setImageIcon("/icons/sunny.svg");
        }

        if (data.code === 2) {
          if (data.cities) {
            setCities(data.cities);
          }
          setImageIcon("/icons/rainy.svg");
        }
        if (data.code === 3) {
          setImageIcon("/icons/rainy.svg");
        }
        if (data.code === 4) {
          setImageIcon("/icons/semiRainy.svg");
        }
        toast({
          variant: "default",
          title: "Success",
          description: data.message,
        });
        setMessage(data.message);
      })
      .catch((error) => console.error(error));
  };

  const buttonText = "Chech proposal for the user";

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(handleSubmit)} className="salaryForm mt-10 space-y-6">
        <FormField
          key="user_id"
          control={form.control}
          name={"user_id" as Path<T>}
          render={() => (
            <FormItem className="flex w-full flex-col gap-2.5">
              <FormLabel className="paragraph-medium text-dark200_light900">Select user</FormLabel>

              <select
                className="w-full rounded border border-gray-300 px-3 py-2"
                {...form.register("user_id" as Path<T>, { required: true })}
              >
                {users.map((user: User) => (
                  <option key={user.id} value={user.id}>
                    {user.first_name} {user.last_name}
                  </option>
                ))}
              </select>

              <FormMessage />
            </FormItem>
          )}
        />

        <Button
          disabled={form.formState.isSubmitting}
          className="primary-gradient paragraph-medium min-h-12 w-full rounded-2 px-4 py-3 font-inter !text-light-900"
        >
          {form.formState.isSubmitting ? "Loading..." : buttonText}
        </Button>
      </form>
      <br />
      {message && <h3>{message}</h3>}
      <br />
      {cities && (
        <>
          <h3>{`${cities.length > 1 ? "These are cities" : "this is city"} in your country that you can wisit to enjoy the weather.`}</h3>
          <br />
          <ol>
            {cities.map((city, index) => (
              <li key={index}>
                {index + 1}. {city}
              </li>
            ))}
          </ol>
        </>
      )}
      <br />
      {imageIcon && <Image src={imageIcon} width={80} height={80} alt="Weather Icon" className="invert-colors" />}
    </Form>
  );
};

export default ProposeForm;
